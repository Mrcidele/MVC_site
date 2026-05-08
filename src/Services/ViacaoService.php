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
    private AuthService $auth;

    public function __construct(
        ?ViacaoRepository $repo = null,
        ?ViacaoValidator $validator = null,
        ?HistoricoRepository $historico = null,
        ?AuthService $auth = null
    ) {
        $this->repo = $repo ?? new ViacaoRepository();
        $this->validator = $validator ?? new ViacaoValidator();
        $this->historico = $historico ?? new HistoricoRepository();
        $this->auth = $auth ?? new AuthService(); // Injetando serviço de autenticação
    }

    // Recupera viações com lógica de cache integrada para a Home.
    public function all(string $busca, string $status, string $ordem, string $dir): array
    {
        $isHomeQuery = ($busca === '' && $status === 'ativo' && $ordem === 'nome' && $dir === 'ASC');

        if ($isHomeQuery) {
            $cached = \getCachedData('viacoes_ativas');
            if ($cached !== null) {
                return array_map(fn($row) => Viacao::fromRow($row), $cached);
            }
        }

        $viacoes = $this->repo->all($busca, $status, $ordem, $dir);

        if ($isHomeQuery) {
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

        $usuarioId = $this->auth->getLoggedUserId();
        $this->historico->log($id, 'Criado', "Viação '{$nome}' cadastrada.", $usuarioId);

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
            $usuarioId = $this->auth->getLoggedUserId();
            $this->historico->log($id, 'Editado', json_encode($mudancas, JSON_UNESCAPED_UNICODE), $usuarioId);
        }

        \invalidateCache('viacoes_ativas');
    }

    public function delete(int $id): void
    {
        $viacao = $this->repo->find($id);

        if (!$viacao) {
            return;
        }

        $usuarioId = $this->auth->getLoggedUserId();

        $this->historico->log($id, 'Excluido', "Viação '{$viacao->nome}' foi excluída.", $usuarioId);
        $this->repo->delete($id);

        if ($viacao->logo && file_exists(__DIR__ . '/../../public/uploads/logos/' . $viacao->logo)) {
            unlink(__DIR__ . '/../../public/uploads/logos/' . $viacao->logo);
        }

        \invalidateCache('viacoes_ativas');
    }

    // Processamento interno de arquivos de imagem.
    private function handleUpload(?array $file): ?string
    {
        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) return null;

        $tmpName = $file['tmp_name'];

        if (!is_uploaded_file($tmpName)) {
            throw new Exception('Arquivo de upload inválido.');
        }

        $mime = mime_content_type($tmpName);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $allowedMimes)) {
            throw new Exception('Apenas imagens JPG, PNG ou WEBP são permitidas.');
        }

        $extensaoSegura = $allowedMimes[$mime];
        $nomeLogo = uniqid('logo_') . '.' . $extensaoSegura;
        $dir = dirname(__DIR__, 2) . '/src/public/uploads/logos/';

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        if (!move_uploaded_file($tmpName, $dir . $nomeLogo)) {
            throw new Exception('Falha ao mover a imagem para o diretório.');
        }

        return $nomeLogo;
    }
}