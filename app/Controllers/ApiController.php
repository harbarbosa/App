<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
class ApiController extends Security_Controller
{
    private $client;
    private $authUrl = 'https://api.eugestor.insidesistemas.com.br/api/auth';
    

    public function __construct()
    {
        // Inicializa o cliente HTTP
        $this->client = new Client();
    }

    /**
     * Método principal para executar a lógica.
     */
    public function fetchData()
    {

        $this->GetProdutosOS();
        
        
    }

    public function requestDataPost($dataUrl, $data)
    {

        $token = $this->getToken();

        if (!$token) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Falha ao autenticar na API.',
            ]);
        }

        try {
            // Faz a requisição com o token
            $headers = [
                'Authorization' => "Bearer {$token}",
            ];

            $response = $this->client->post($dataUrl, [
                'headers' => $headers,
                'json' => $data, 
            ]);

            $responseBody = $response->getBody()->getContents();
             $data = json_decode($responseBody, true);

     
        // Retorna os dados em JSON, se necessário
        return $data;

          

            

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }



    }



    public function requestData($dataUrl)
{
    $token = $this->getToken();

    if (!$token) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Falha ao autenticar na API.',
        ]);
    }

    try {
        // Faz a requisição com o token
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $response = $this->client->get($dataUrl, [
            'headers' => $headers,
        ]);

        // Obtem o corpo da resposta apenas uma vez
        $responseBody = $response->getBody()->getContents();
        $data = json_decode($responseBody, true);

     
        // Retorna os dados em JSON, se necessário
        return $data;

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
}

    private function getToken()
    {
        $session = session();

        // Verifica se o token está na sessão
        if ($session->has('api_token')) {
            return $session->get('api_token');
        }

        // Caso não tenha o token, realiza a autenticação
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = json_encode([
            'email' => 'henrique.barbosa@alfahp.com.br',
            'senha' => 'relojo',
            'isLembrarSessao' => false,
        ]);

        try {
            $response = $this->client->post($this->authUrl, [
                'headers' => $headers,
                'body' => $body,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

         

            if (isset($data['data'])) {
                // Armazena o token na sessão
                $session->set('api_token', $data['data']);
                return $data['data'];
            }
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
        }

        return null; // Falha na autenticação
    }

    public function updateClients()
    {   
        $dataUrl = 'https://api.eugestor.insidesistemas.com.br/api/pessoas?apenasComContrato=false&exibirInativos=false&pagina=17&tamanhoPagina=50&decrescente=true';
    
        //VERIFICA NUMERO DE PAGINAS
    
        $apiResponse = $this->requestData($dataUrl);
        $totalRegistros = $apiResponse['data']['totalRegistros'];
    
        $paginas = ceil($totalRegistros / 50);
        $contador = 1;
       
    
        while ($contador <= $paginas) {
            
            $dataUrl = 'https://api.eugestor.insidesistemas.com.br/api/pessoas?apenasComContrato=false&exibirInativos=false&pagina='.$contador.'&tamanhoPagina=50&decrescente=true';
            $apiResponse = $this->requestData($dataUrl);
    
            $records = $apiResponse['data']['lista'] ?? [];
             // Conexão com o banco
            $db = \Config\Database::connect();
            $builder = $db->table('rise_clients');
    
            foreach ($records as $record) {
                // Prepara os dados para salvar
                $dataToSave = [
                    'id' => $record['pessoaId'],
               
                    'company_name' => $record['nomeRazao'],
                    'nome_fantasia' => $record['apelidoFantasia'],
                    'website' => $record['contatoPrincipal']['email'] ?? null,
                    'phone' => $record['contatoPrincipal']['telefone'] ?? null,
                ];
        
                // Verifica se o registro já existe no banco
                $existing = $builder->where('id', $record['pessoaId'])->get()->getRow();
        
                if ($existing) {
                    // Atualiza registro existente
                    $builder->where('id', $record['pessoaId'])->update($dataToSave);
                } else {
                    // Insere novo registro
                    $builder->insert($dataToSave);
                }
            }
          
           
            $contador++;
        }
    
        echo 'Clientes atualizado com sucesso';
       
    }
    
    public function updateProdutos()
    {   
        ini_set('max_execution_time', 300); // Tempo em segundos
        $dataUrl = 'https://api.eugestor.insidesistemas.com.br/api/produtos?exibirInativos=false&pagina=1&tamanhoPagina=50&ordenarPor=produtoId&decrescente=true';
    
        //VERIFICA NUMERO DE PAGINAS
    
        $apiResponse = $this->requestData($dataUrl);
        $totalRegistros = $apiResponse['data']['totalRegistros'];
    
        $paginas = ceil($totalRegistros / 50);
        $contador = 1;
       
      
    
        while ($contador <= $paginas) {
            
            $dataUrl = 'https://api.eugestor.insidesistemas.com.br/api/produtos?exibirInativos=false&pagina='.$contador.'&tamanhoPagina=50&ordenarPor=produtoId&decrescente=true';
            
            
            $apiResponse = $this->requestData($dataUrl);
    
            $dados = $apiResponse['data']['lista'] ?? [];
    
            
             // Conexão com o banco
            $db = \Config\Database::connect();
            $builder = $db->table('rise_items');
    
            foreach ($dados as $dado) {
    
                $produtoId =  $dado['produtoId'];
    
                $existing = $builder->where('id', $produtoId)->get()->getRow();
                
                if ($existing){
                    break;
                }
               
                $dataUrl = 'https://api.eugestor.insidesistemas.com.br/graphql';
                $data = [
                    "operationName" => "getProduto",
                    "variables" => [
                        "produtoId" => $produtoId 
                    ],
                    "query" => "query getProduto(\$produtoId: Long!) {\n  produto(query: {produtoId: \$produtoId}) {\n    produtoId\n    codigoBarras\n    codigoFornecedor\n    dataRegistro\n    dataUltimaAlteracao\n    descricao\n    estoqueNegativoPermitido\n    grupoProduto {\n      descricao\n      isAtivo\n      grupoProdutoId\n      __typename\n    }\n    isAtivo\n    locacaoPermitida\n    marca {\n      descricao\n      isAtivo\n      marcaId\n      __typename\n    }\n    modelo\n    empresas {\n      empresaId\n      cnpj\n      fantasia\n      razaoSocial\n      __typename\n    }\n    tipoRastreabilidade\n    unidadeMedida\n    usuarioRegistroId\n    produtoDadosTributarios {\n      cest\n      ncm\n      origemProduto\n      produtoId\n      __typename\n    }\n    tributacaoIpiProduto {\n      aliquota\n      situacaoTributaria\n      __typename\n    }\n    tributacaoIcms {\n      codigoBeneficioFiscal\n      situacaoTributariaIcms\n      valores {\n        aliquotaIcmsSt\n        aliquotaMvaIcms\n        aliquotaReducaoBaseIcmsSt\n        aliquotaReducaoBaseIcms\n        aliquotaDiferimentoIcms\n        baseIcmsStRetidoAnteriormente\n        valorIcmsStRetidoAnteriormente\n        pSt\n        vIcmsSubstituto\n        __typename\n      }\n      __typename\n    }\n    valores {\n      custo\n      percentualLucro\n      preco\n      usaMargemPadraoParaCalcularPreco\n      __typename\n    }\n    historico(pagina: 1) {\n      tamanhoPagina\n      totalRegistros\n      lista {\n        dataHora\n        descricao\n        nomeUsuario\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  configuracaoComercial {\n    percentualLucroPadrao\n    __typename\n  }\n}"
                ];
    
                $apiResponse = $this->requestDataPost($dataUrl, $data);
    
                $record = $apiResponse['data']['produto'] ?? [];
    
            
       
    
                // Prepara os dados para salvar
                $dataToSave = [
                    'id' => $record['produtoId'],
               
                    'title' => $record['descricao'],
                    'rate' => $record['valores']['custo'],
                    'p_venda' => $record['valores']['preco'],
                    'unit_type' => $record['unidadeMedida'],
                    'bdi' => $record['valores']['percentualLucro'],
                ];
        
                // Verifica se o registro já existe no banco
                $existing = $builder->where('id', $record['produtoId'])->get()->getRow();
        
                if ($existing) {
                    // Atualiza registro existente
                    $builder->where('id', $record['produtoId'])->update($dataToSave);
                } else {
                    // Insere novo registro
                    $builder->insert($dataToSave);
                }
            }
          
           
            $contador++;
        }
    
        echo 'Produtos atualizado com sucesso';
       
    }

    public function OpenOS()
    {
        try {
            // Configura os cabeçalhos da requisição
            $headers = [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Encoding' => 'gzip, deflate, br, zstd',
                'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'Cookie' => 'transient-session-key=; osVisitor=0bbcfbda-1ffb-43ba-a16e-8f31c59903b3; DEVICE_TYPE=desktop; DEVICE_ORIENTATION=undefined; ASP.NET_SessionId=ga1kq1yw41bdpxbn30cf5bbt; _clck=7jwmrf%7C2%7Cft6%7C0%7C1847; osVisit=1b323f70-44aa-453a-ace9-17be3e4a5e3e; EuGestor.sid=629210252897339715413634363394142515439; EuGestor=37bbcf47-92ff-4835-8bca-def3d70b8be5; pageLoadedFromBrowserCache=true; _clsk=16ra4ba%7C1738715866499%7C17%7C1%7Cp.clarity.ms%2Fcollect',
                'Host' => 'sistema.eugestor.app',
                'Pragma' => 'no-cache',
                'Sec-Ch-Ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'Sec-Ch-Ua-Mobile' => '?0',
                'Sec-Ch-Ua-Platform' => '"Windows"',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-User' => '?1',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ];
    
            // Faz a requisição GET
            $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx', [
                'headers' => $headers,
            ]);
    
            // Obtém o HTML da resposta
            $html = $response->getBody()->getContents();
    
            // Processa o HTML para extrair os dados das OS
            $html2 = $this->parseMultiplasOS($html);

           
    
            // Conexão com o banco de dados
            $db = \Config\Database::connect();
            $builder = $db->table('rise_projects');
            $bd_cliente = $db->table('rise_clients');

            //echo var_dump($html2);
            echo $html;
        
            foreach ($html2 as $os) {
                
                $numeroOS = $os['numeroOS'];
                $cliente = $os['nomeCliente'];
                $codigoOs = $os['codigoOS'];
                $clienteID = $this->getClienteOS($codigoOs);


                
                // Verifica se o projeto já existe
                $existingProject = $builder->where('id', $numeroOS)->get()->getRow();
    
                if (!$existingProject) {
                    // Verifica se o cliente já existe
                    $client = $bd_cliente->where('nome_fantasia', $cliente)->get()->getRow();
    
                 
    
                    // Prepara os dados para salvar o projeto
                    $dataToSave = [
                        'id' => $numeroOS,
                        'client_id' => 11,
                        'ordem_servico' => $codigoOs,
                        // Adicione outros campos necessários aqui
                    ];
    
                    // Insere o novo projeto no banco de dados
                    $builder->insert($dataToSave);
    
                    echo "Novo projeto criado para o número OS: $numeroOS\n";
                } else {
                    echo "Projeto com número OS $numeroOS já existe.\n";
                }
            }
    
            // Retorna uma resposta de sucesso (opcional)
        
    
        } catch (\Exception $e) {
            // Retorna uma resposta de erro em formato JSON
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
   public function parseMultiplasOS($htmlContent)
   {
       $dom = new \DOMDocument();
       libxml_use_internal_errors(true);
       $dom->loadHTML($htmlContent);
       libxml_clear_errors();
   
       $xpath = new \DOMXPath($dom);
       $dadosOS = [];
   
       // Seleciona todos os links que possuem OrdemServicoId no href
       $osLinks = $xpath->query("//a[contains(@href, 'OrdemServicoId=')]");
   
       foreach ($osLinks as $osLink) {
           $osData = [];
   
           // Extrair código OrdemServicoId
           $href = $osLink->getAttribute("href");
           if (preg_match("/OrdemServicoId=(\d+)/", $href, $matches)) {
               $osData['codigoOS'] = $matches[1];
           }
   
           // Capturar e ajustar os dados do número da OS e cliente
           $textoOS = trim($osLink->textContent);
   
           // Extrair númeroOS (primeiros 6 caracteres numéricos)
           if (preg_match('/^\d{6}/', $textoOS, $numeroMatch)) {
               $osData['numeroOS'] = $numeroMatch[0];
           } else {
               $osData['numeroOS'] = 'Desconhecido';
           }
   
           // Extrair nomeCliente (do texto completo após o número da OS)
           $nomeCliente = preg_replace('/^\d{6} -\s*/', '', $textoOS);
           $nomeCliente = preg_replace('/\s*\(.+\).*$/', '', $nomeCliente);  // Remove possíveis parenteses e observações
           $osData['nomeCliente'] = trim($nomeCliente);
   
           // Verifica se os dados da OS são válidos antes de adicionar ao array
           if (!empty($osData['codigoOS']) && $osData['numeroOS'] !== 'Desconhecido' && !empty($osData['nomeCliente'])) {
               $dadosOS[] = $osData;
           }
       }
   
       return $dadosOS;
   }

   public function getClienteOS($OsId)
   {
    try {
        // Configura os cabeçalhos da requisição
        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Encoding' => 'gzip, deflate, br, zstd',
            'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Cookie' => 'transient-session-key=; osVisitor=0bbcfbda-1ffb-43ba-a16e-8f31c59903b3; DEVICE_TYPE=desktop; DEVICE_ORIENTATION=undefined; ASP.NET_SessionId=ga1kq1yw41bdpxbn30cf5bbt; _clck=7jwmrf%7C2%7Cft6%7C0%7C1847; osVisit=1b323f70-44aa-453a-ace9-17be3e4a5e3e; EuGestor.sid=629210252897339715413634363394142515439; EuGestor=37bbcf47-92ff-4835-8bca-def3d70b8be5; pageLoadedFromBrowserCache=true; _clsk=16ra4ba%7C1738715866499%7C17%7C1%7Cp.clarity.ms%2Fcollect',
            'Host' => 'sistema.eugestor.app',
            'Pragma' => 'no-cache',
            'Sec-Ch-Ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
            'Sec-Ch-Ua-Mobile' => '?0',
            'Sec-Ch-Ua-Platform' => '"Windows"',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        ];

        // Faz a requisição GET
        $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx?OrdemServicoId='.$OsId.'&IsEditando=True', [
            'headers' => $headers,
        ]);

        // Obtém o HTML da resposta
        $html = $response->getBody()->getContents();

        $cliente = $this->extrair_nome_empresa($html); 

        echo $cliente;
        exit;
        preg_match('/Ordem de Serviço: (\d+)([A-Za-z]+ - .+)/', $cliente, $matches);

        // Verificando se a correspondência foi encontrada
        if (isset($matches[1]) && isset($matches[2])) {
            $numero_os = $matches[1]; // Número da OS
            $nome_empresa = ltrim($matches[2], ' -'); // Nome da empresa (remove o prefixo " - ")
    
        } 



    
        return $nome_empresa;

        
        

    } catch (\Exception $e) {
        // Retorna uma resposta de erro em formato JSON
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
}

function extrair_nome_empresa(string $html): ?string
    {
        // Cria uma nova instância do DOMDocument
        $dom = new \DOMDocument();

        // Suprime os erros de HTML malformado
        libxml_use_internal_errors(true);

        // Carrega o HTML no DOMDocument
        $dom->loadHTML($html);

        // Limpa os erros
        libxml_clear_errors();

        // Cria uma instância do DOMXPath para consultar o documento
        $xpath = new \DOMXPath($dom);

        // Consulta XPath para encontrar o elemento que contém o nome da empresa
        // Aqui, estamos procurando por um <div> com a classe "Cell2" que contém o nome da empresa
        $elements = $xpath->query("//div[contains(@class, 'Cell2')]");

        // Verifica se o elemento foi encontrado
        if ($elements->length > 0) {
            // Retorna o texto do primeiro elemento encontrado
            return trim($elements->item(2)->nodeValue);
        }

        // Retorna null se o nome da empresa não for encontrado
        return null;
    }
   
   
   

    public function GetProdutosOS()
    {
        $id = 288;

       try {
           // Configura os cabeçalhos da requisição
           $headers = [
               'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
               'Accept-Encoding' => 'gzip, deflate, br, zstd',
               'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
               'Cache-Control' => 'no-cache',
               'Connection' => 'keep-alive',
               'Cookie' => 'transient-session-key=; osVisitor=0bbcfbda-1ffb-43ba-a16e-8f31c59903b3; DEVICE_TYPE=desktop; DEVICE_ORIENTATION=undefined; ASP.NET_SessionId=ga1kq1yw41bdpxbn30cf5bbt; _clck=7jwmrf%7C2%7Cft6%7C0%7C1847; osVisit=1b323f70-44aa-453a-ace9-17be3e4a5e3e; EuGestor.sid=629210252897339715413634363394142515439; EuGestor=37bbcf47-92ff-4835-8bca-def3d70b8be5; pageLoadedFromBrowserCache=true; _clsk=16ra4ba%7C1738715866499%7C17%7C1%7Cp.clarity.ms%2Fcollect',
               'Host' => 'sistema.eugestor.app',
               'Pragma' => 'no-cache',
               'Referer' => 'https://sistema.eugestor.app/Operacional/OrdemServico.aspx?OrdemServicoId=243&IsEditando=True',
               'Sec-Ch-Ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
               'Sec-Ch-Ua-Mobile' => '?0',
               'Sec-Ch-Ua-Platform' => '"Windows"',
               'Sec-Fetch-Dest' => 'document',
               'Sec-Fetch-Mode' => 'navigate',
               'Sec-Fetch-Site' => 'same-origin',
               'Sec-Fetch-User' => '?1',
               'Upgrade-Insecure-Requests' => '1',
               'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
           ];
   
           // Faz a requisição GET
           $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx?SubMenu=P&OrdemServicoId='.$id.'', [
               'headers' => $headers,
           ]);
   
           // Obtém o HTML da resposta
           $html = $response->getBody()->getContents();
   
           // Exibe o HTML diretamente (apenas para testes, remova em produção)
           $html2 = $this->parseProdutosFromHtml($html);
      
          // echo var_dump($html2);
           //exit;
           $db = \Config\Database::connect();
           $builder = $db->table('rise_items');
          


         
            foreach ($html2 as $itemEugestor) {
                
                $items = $builder->where('title', $itemEugestor['nome'])->get()->getRow();
                echo $itemEugestor['nome'].' - '.$itemEugestor['quantidade'].' - Custo = '.$items->rate;
                echo '----------------';
                
            }
   
           // Retorna os dados processados, se necessário
           // return $data;
   
       } catch (\Exception $e) {
           // Retorna uma resposta de erro em formato JSON
           return $this->response->setJSON([
               'status' => 'error',
               'message' => $e->getMessage(),
           ]);
       }
   }

   public function parseProdutosFromHtml($htmlContent)
   {
       $dom = new \DOMDocument();
   
       // Suprimir avisos devido a HTML malformado
       libxml_use_internal_errors(true);
       $dom->loadHTML($htmlContent);
       libxml_clear_errors();
   
       $xpath = new \DOMXPath($dom);
   
       // Seleciona todas as linhas de produtos dentro do tbody
       $linhasProdutos = $xpath->query("//tbody/tr");
   
       $produtos = [];
       foreach ($linhasProdutos as $linha) {
           $colunas = $linha->getElementsByTagName("td");
   
           if ($colunas->length >= 5) {
               $produto = [
                   'nome' => trim($colunas->item(0)->textContent),
                   'quantidade' => trim($colunas->item(1)->textContent),
                   'valor_unitario' => trim($colunas->item(2)->textContent),
                   'desconto' => trim($colunas->item(3)->textContent),
                   'subtotal' => trim($colunas->item(4)->textContent),
               ];
               $produtos[] = $produto;
           }
       }
   
       return $produtos;
   }
   


}
