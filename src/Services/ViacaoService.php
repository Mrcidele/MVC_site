<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Viacao;
use App\DTOs\ViacaoDTO;
use App\Repositories\ViacaoRepository;
use App\Repositories\HistoricoRepository;
use App\Validators\ViacaoValidator;
use Exception;

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
        $this->auth = $auth ?? new AuthService();
    }

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

    /**
     * Criação utilizando DTO.
     */
    public function create(ViacaoDTO $dto): int
    {
        // Validação usando o array do DTO
        $errors = $this->validator->validate($dto->toArray());
        if ($errors !== []) throw new Exception(implode('|', $errors));

        $data = $dto->toArray();
        $data['logo'] = $this->handleUpload($dto->logoFile);

        $id = $this->repo->create($data);

        $usuarioId = $this->auth->getLoggedUserId();
        $this->historico->log($id, 'Criado', "Viação '{$dto->nome}' cadastrada.", $usuarioId);

        \invalidateCache('viacoes_ativas');

        return $id;
    }

    /**
     * Atualização utilizando DTO e detecção de mudanças imutáveis.
     */
    public function update(int $id, ViacaoDTO $dto): void
    {
        $old = $this->repo->find($id);
        if (!$old) throw new Exception('Viação não encontrada.');

        $errors = $this->validator->validate($dto->toArray());
        if ($errors !== []) throw new Exception(implode('|', $errors));

        $updateData = $dto->toArray();
        $mudancas = [];

        // Comparação de mudanças para o Log de Auditoria
        if ($old->nome !== $dto->nome) $mudancas[] = ['campo' => 'Nome', 'de' => $old->nome, 'para' => $dto->nome];
        if ($old->url !== $dto->url) $mudancas[] = ['campo' => 'URL', 'de' => $old->url, 'para' => $dto->url];
        if ($old->cidade !== $dto->cidade) $mudancas[] = ['campo' => 'Cidade', 'de' => $old->cidade, 'para' => $dto->cidade];
        if ($old->status !== $dto->status) $mudancas[] = ['campo' => 'Status', 'de' => $old->status, 'para' => $dto->status];

        if ($dto->logoFile !== null) {
            $updateData['logo'] = $this->handleUpload($dto->logoFile);
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
        if (!$viacao) return;

        $usuarioId = $this->auth->getLoggedUserId();
        $this->historico->log($id, 'Excluido', "Viação '{$viacao->nome}' foi excluída.", $usuarioId);

        $this->repo->delete($id);

        if ($viacao->logo && file_exists(__DIR__ . '/../../public/uploads/logos/' . $viacao->logo)) {
            unlink(__DIR__ . '/../../public/uploads/logos/' . $viacao->logo);
        }

        \invalidateCache('viacoes_ativas');
    }

    private function handleUpload(?array $file): ?string
    {
        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) return null;

        $tmpName = $file['tmp_name'];
        if (!is_uploaded_file($tmpName)) throw new Exception('Arquivo de upload inválido.');

        $mime = mime_content_type($tmpName);
        $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!array_key_exists($mime, $allowedMimes)) {
            throw new Exception('Apenas imagens JPG, PNG ou WEBP são permitidas.');
        }

        $nomeLogo = uniqid('logo_') . '.' . $allowedMimes[$mime];
        $dir = dirname(__DIR__, 2) . '/src/public/uploads/logos/';

        if (!is_dir($dir)) mkdir($dir, 0755, true);
        if (!move_uploaded_file($tmpName, $dir . $nomeLogo)) throw new Exception('Falha ao mover a imagem.');

        return $nomeLogo;
    }
}