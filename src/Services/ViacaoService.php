<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Viacao;
use App\Repositories\ViacaoRepository;
use App\Repositories\HistoricoRepository;
use App\Validators\ViacaoValidator;
use Exception;

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

    public function all(string $busca, string $status, string $ordem, string $dir): array
    {
        return $this->repo->all($busca, $status, $ordem, $dir);
    }

    public function find(int $id): ?Viacao
    {
        return $this->repo->find($id);
    }

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
        return $id;
    }

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
            $this->historico->log($id, 'Editado', json_encode($mudancas, JSON_UNESCAPED_UNICODE));
        }
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
        }
    }

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