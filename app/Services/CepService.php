<?php

declare(strict_types=1);

namespace App\Services;

class CepService
{
    private string $baseUrl = 'https://viacep.com.br/ws/';

    public function getCepData(string $cep): array
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return [
                'success' => false,
                'message' => 'CEP deve conter 8 dígitos',
                'data' => null
            ];
        }

        $url = $this->baseUrl . $cep . '/json/';
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Teste Montink/1.0',
                        'Accept: application/json'
                    ]
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                return [
                    'success' => false,
                    'message' => 'Erro ao consultar CEP',
                    'data' => null
                ];
            }

            $data = json_decode($response, true);

            if (isset($data['erro']) && $data['erro'] === true) {
                return [
                    'success' => false,
                    'message' => 'CEP não encontrado',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'CEP encontrado com sucesso',
                'data' => [
                    'cep' => $data['cep'] ?? '',
                    'logradouro' => $data['logradouro'] ?? '',
                    'complemento' => $data['complemento'] ?? '',
                    'bairro' => $data['bairro'] ?? '',
                    'localidade' => $data['localidade'] ?? '',
                    'uf' => $data['uf'] ?? '',
                    'ibge' => $data['ibge'] ?? '',
                    'gia' => $data['gia'] ?? '',
                    'ddd' => $data['ddd'] ?? '',
                    'siafi' => $data['siafi'] ?? ''
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro interno ao consultar CEP: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function formatCep(string $cep): string
    {
        $cep = preg_replace('/\D/', '', $cep);
        if (strlen($cep) === 8) {
            return substr($cep, 0, 5) . '-' . substr($cep, 5);
        }
        return $cep;
    }

    public function isValidCep(string $cep): bool
    {
        $cep = preg_replace('/\D/', '', $cep);
        return strlen($cep) === 8 && ctype_digit($cep);
    }
}