<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Viacao;
use App\Repositories\ViacaoRepository;
use App\Repositories\HistoricoRepository;
use App\Validators\ViacaoValidator;
use Exception;

// Camada de Serviço: Orquestra regras de negócio, persistência e cache.
final class ViacaoService
{
    private ViacaoRepository $repo;
    private ViacaoValidator $validator;
    private HistoricoRepository $historico;

    public function __construct(
        ?ViacaoRepository $repo = null,
        ?ViacaoValidator $validator = null,
        ?HistoricoRepository $historico = null
    ) {
        $this->repo = $repo ?? new ViacaoRepository();
        $this->validator = $validator ?? new ViacaoValidator();
        $this->historico = $historico ?? new HistoricoRepository();
    }

    // Recupera viações com lógica de cache integrada para a Home.
    public function all(string $busca, string $status, string $ordem, string $dir): array
    {
        // Verifica se é a consulta padrão da Home para usar o cache
        $isHomeQuery = ($busca === '' && $status === 'ativo' && $ordem === 'nome' && $dir === 'ASC');

        if ($isHomeQuery) {
            $cached = \getCachedData('viacoes_ativas');
            if ($cached !== null) {
                // Converte os arrays do JSON de volta para objetos Viacao
                return array_map(fn($row) => Viacao::fromRow($row), $cached);
            }
        }

        // Se não usou cache ou o cache falhou (miss), busca no banco de dados
        $viacoes = $this->repo->all($busca, $status, $ordem, $dir);

        // Se for a consulta da home, salva no cache para as próximas requisições
        if ($isHomeQuery) {
            // Converte os objetos Viacao para array simples antes de salvar no JSON
            $dataToCache = array_map(fn($v) => (array) $v, $viacoes);
            \setCachedData('viacoes_ativas', $dataToCache);
        }

        return $viacoes;
    }

    public function find(int $id): ?Viacao
    {
        return $this->repo->find($id);
    }

    // Criação de viação com validação, upload e auditoria.
    public function create(array $data, ?array $fileLogo = null): int
    {
        $errors = $this->validator->validate($data);
        if ($errors !== []) throw new Exception(implode('|', $errors));

        $nome = trim($data['nome']);
        $id = $this->repo->create([
            'nome'   => $nome,
            'url'    => trim($data['url']),
            'cidade' => trim($data['cidade']),
            'status' => ($data['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
            'logo'   => $this->handleUpload($fileLogo),
        ]);

        $this->historico->log($id, 'Criado', "Viação '{$nome}' cadastrada.");

        // Limpa o cache após criar uma nova viação
        \invalidateCache('viacoes_ativas');

        return $id;
    }

    // Atualização com detecção de mudanças para o histórico.
    public function update(int $id, array $data, ?array $fileLogo = null): void
    {
        $old = $this->repo->find($id);
        if (!$old) throw new Exception('Viação não encontrada.');

        $errors = $this->validator->validate($data);
        if ($errors !== []) throw new Exception(implode('|', $errors));

        $updateData = [
            'nome'   => trim($data['nome']),
            'url'    => trim($data['url']),
            'cidade' => trim($data['cidade']),
            'status' => ($data['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
        ];

        // Mapeia o que mudou para salvar no histórico
        $mudancas = [];
        if ($old->nome !== $updateData['nome']) $mudancas[] = ['campo' => 'Nome', 'de' => $old->nome, 'para' => $updateData['nome']];
        if ($old->url !== $updateData['url']) $mudancas[] = ['campo' => 'URL', 'de' => $old->url, 'para' => $updateData['url']];
        if ($old->cidade !== $updateData['cidade']) $mudancas[] = ['campo' => 'Cidade', 'de' => $old->cidade, 'para' => $updateData['cidade']];
        if ($old->status !== $updateData['status']) $mudancas[] = ['campo' => 'Status', 'de' => $old->status, 'para' => $updateData['status']];

        if ($fileLogo !== null && $fileLogo['error'] === UPLOAD_ERR_OK) {
            $updateData['logo'] = $this->handleUpload($fileLogo);
            $mudancas[] = ['campo' => 'Logo', 'de' => 'Imagem anterior', 'para' => 'Nova imagem'];
        }

        $this->repo->update($id, $updateData);

        if (!empty($mudancas)) {
            $this->historico->log($id, 'Editado', json_encode($mudancas, JSON_UNESCAPED_UNICODE));
        }

        // Limpa o cache após editar uma viação
        \invalidateCache('viacoes_ativas');
    }

    public function delete(int $id): void
    {
        $viacao = $this->repo->find($id);
        if ($viacao) {
            $this->repo->delete($id);
            $this->historico->log($id, 'Excluido', "Viação '{$viacao->nome}' foi excluída.");

            if ($viacao->logo && file_exists(__DIR__ . '/../../public/uploads/logos/' . $viacao->logo)) {
                unlink(__DIR__ . '/../../public/uploads/logos/' . $viacao->logo);
            }

            // Limpa o cache após excluir uma viação
            \invalidateCache('viacoes_ativas');
        }
    }

    // Processamento interno de arquivos de imagem.
    private function handleUpload(?array $file): ?string
    {
        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) return null;

        $tmpName = $file['tmp_name'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array(mime_content_type($tmpName), $allowed, true)) {
            throw new Exception('Apenas imagens JPG, PNG ou WEBP são permitidas.');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nomeLogo = uniqid('logo_') . '.' . $ext;
        $dir = dirname(__DIR__, 2) . '/src/public/uploads/logos/';

        if (!is_dir($dir)) mkdir($dir, 0755, true);
        move_uploaded_file($tmpName, $dir . $nomeLogo);

        return $nomeLogo;
    }
}