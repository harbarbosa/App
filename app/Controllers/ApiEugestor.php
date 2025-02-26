<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
class ApiEugestor extends Controller
{
    private $client;
    private $authUrl = 'https://api.eugestor.insidesistemas.com.br/api/auth';
    

    public function __construct()
    {
        // Inicializa o cliente HTTP
        $this->client = new Client();
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
                if($record['apelidoFantasia'] == ''){
                    $company_name = $record['nomeRazao'];
                }else{
                    $company_name = $record['apelidoFantasia'];
                }
                $dataToSave = [
                    'id' => $record['pessoaId'],
                    'nome_razao' => $record['nomeRazao'],
                    'company_name' => $company_name,
                    'website' => $record['contatoPrincipal']['email'] ?? null,
                    'phone' => $record['contatoPrincipal']['telefone'] ?? null,
                ];
        
                // Verifica se o registro já existe no banco
                $existing = $builder->where('id', $record['pessoaId'])->get()->getRow();
        
                if ($existing) {
                    unset($dataToSave['id']);
                    // Atualiza registro existente
                    $builder->where('id', $record['pessoaId'])->update($dataToSave);
                } else {
                    // Insere novo registro
                    $builder->insert($dataToSave);
                }
            }
        
        
            $contador++;
        }

        echo 'Clientes atualizado com sucesso<br>';
    
    }

    public function updateProdutos()
    {   
        set_time_limit(0);
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
                    unset($dataToSave['id']);
                    $builder->where('id', $record['produtoId'])->update($dataToSave);
                } else {
                    // Insere novo registro
                    $builder->insert($dataToSave);
                }
            }
        
        
            $contador++;
        }

        echo 'Produtos atualizado com sucesso<br>';
    
    }

    public function UpdateOS()
    {

        set_time_limit(0);

        $db = \Config\Database::connect();
        $builder = $db->table('rise_projects');
    
        $projetos = $builder->where('status', 'open')
        ->where('deleted', 0)
        ->get()
        ->getResult();

     
    
        foreach($projetos as $projeto){

            echo $projeto->id.'<br>';
           

            $project_id = $projeto->id;
            $Osid = $projeto->ordem_servico;

            if($this->GetOS()){
                echo 'OSs atualizados com sucesso na OS<br>';
               }else{
                echo 'erro';
               }


           if($this->GetProdutosOS($project_id, $Osid)){
            echo 'Prudutos atualizados com sucesso na OS<br>';
           }else{
            echo 'erro';
           }

           if($this->GetAtividadeOS($project_id, $Osid)){
            echo 'Atividades atualizados com sucesso na OS<br>';
           }else{
            echo 'erro';
           }

           if($this->GetAtividadeOS($project_id, $Osid)){
            echo 'Atividades atualizados com sucesso na OS<br>';
           }else{
            echo 'erro';
           }

        

        }
    }

    public function GetOS()
    {
    

               
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
           $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx', [
               'headers' => $headers,
           ]);
   
           // Obtém o HTML da resposta
           $html = $response->getBody()->getContents();
   
           // Exibe o HTML diretamente (apenas para testes, remova em produção)
           
           
            $dom = new \DOMDocument();
            
            // Suprimir avisos devido a HTML malformado
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
        
            $xpath = new \DOMXPath($dom);
        
            // Selecionar todos os links <a> que contêm "OrdemServicoId=" no atributo href
            $nodes = $xpath->query("//a[contains(@href, 'OrdemServicoId=')]");
        
            $ordemServicoIds = [];

       
            foreach ($nodes as $node) {
                $href = $node->getAttribute('href');
            
                // Extrair OrdemServicoId da URL usando expressão regular
                if (preg_match('/OrdemServicoId=(\d+)/', $href, $matches)) {
                    $id = $matches[1]; // Captura o ID numérico
                    
                    // Adiciona ao array apenas se ainda não existir
                    if (!in_array($id, $ordemServicoIds)) {
                        $ordemServicoIds[] = $id;
                    }
                }
            }

            $db = \Config\Database::connect();
            $builder = $db->table('rise_projects');

            
   

            foreach ($ordemServicoIds as $indice => $id) {
                if($id == 0){
                    continue;
                }
                $existing = $builder->where('ordem_servico', $id)->get()->getRow();

                if(!$existing){

                  

                    $dadosOS = $this->dadosOS($id);
                 
                    $clienteId = $this->getPessoa($dadosOS['cliente']);
                    $OsId = ltrim($dadosOS['ordem_servico'], '0');
                    $dataAbertura = \DateTime::createFromFormat('d/m/Y', $dadosOS['data_abertura'])->format('Y-m-d');
                    

                    $dataToSave = [
                        'id' => $OsId,
                        'client_id' =>$clienteId,
                        'title' =>'GERADO NO EU GESTOR',
                        'created_date' => $dataAbertura,
                        'ordem_servico' => $id,
                                           ];

                    $builder->insert($dataToSave);


                }

                
            }
            
        
            
           // Retorna os dados processados, se necessário
           return true;
   
       
   }


    public function GetProdutosOS($project_id, $OSid)
    {
    

               
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
           $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx?SubMenu=P&OrdemServicoId='.$OSid.'', [
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
           $items_projeto = $db->table('rise_project_items');
          
         
            foreach ($html2 as $itemEugestor) {

               

                $produto = $this->getProduto($itemEugestor['nome']);
                if ($produto){

                    $produtoId = $produto['produtoId'];
                    $custo = $produto['valores']['custo'];
                    $items = $builder->where('id', $produtoId)->get()->getRow();
                    $existing = $items_projeto->where('item_id', $items->id)->get()->getRow();

                    $dataToSave = [
                        'title' => $produto['descricao'],
                        'project_id' =>$project_id,
                        'quantity' => $itemEugestor['quantidade'],
                        'rate' => $custo,
                        'item_id' => $produto['produtoId'],
                    ];
                    
                    if($existing){
                        $items_projeto->where('id', $existing->id)->update($dataToSave);
                    }else{
                        $items_projeto->insert($dataToSave);
                    }
                    

                   
                }else{
                  $existing = $items_projeto->where('title', '[Não Encontrado EuGestor] - '.$itemEugestor['nome'])->get()->getRow();
                  $dataToSave = [
                        'title' => '[Não Encontrado EuGestor] - '.$itemEugestor['nome'],
                        'project_id' =>$project_id,
                        'quantity' => $itemEugestor['quantidade'],
                        'rate' => 0.00,
                        'item_id' => 0,
                    ];

                    if($existing){
                        $items_projeto->where('id', $existing->id)->update($dataToSave);
                    }else{
                        $items_projeto->insert($dataToSave);
                    }
                   
                }

                                
            
            }
        
            
           // Retorna os dados processados, se necessário
           return true;
   
       
   }

   public function GetAtividadeOS($project_id, $OSid)
     {
   
        
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
          $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx?SubMenu=A&OrdemServicoId='.$OSid.'', [
              'headers' => $headers,
          ]);
  
          // Obtém o HTML da resposta
          $html = $response->getBody()->getContents();

        
  
          // Exibe o HTML diretamente (apenas para testes, remova em produção)
          $html2 = $this->parseAtividadeFromHtml($html);

         

          $db = \Config\Database::connect();
          $builder = $db->table('rise_items');
          $tempo_projeto = $db->table('rise_project_time');
         
        
           foreach ($html2 as $atividadeEugestor) {

            $data = trim($atividadeEugestor['data']);  
            $horaInicio = trim($atividadeEugestor['hora_inicio']);  
            $horaFim = trim($atividadeEugestor['hora_fim']);  

            // Remover espaços comuns e caracteres invisíveis
            $data = preg_replace('/[\s\xA0]+/u', '', $data);
            $horaInicio = preg_replace('/[\s\xA0]+/u', '', $horaInicio);
            $horaFim = preg_replace('/[\s\xA0]+/u', '', $horaFim);

            // Criar objetos DateTime
            $datainicio = \DateTime::createFromFormat("d/m/Y H:i", $data . ' ' . $horaInicio);
            $datafim = \DateTime::createFromFormat("d/m/Y H:i", $data . ' ' . $horaFim);

            // Verificar se a conversão foi bem-sucedida antes de formatar
            if ($datainicio && $datafim) {
                $datainicioFormatado = $datainicio->format("Y-m-d H:i:s");
                $datafimFormatado = $datafim->format("Y-m-d H:i:s");

            
            } else {
                echo "Erro ao converter as datas!" . PHP_EOL;
                print_r(\DateTime::getLastErrors()); // Mostrar possíveis erros
            }
            
            $tecnicos = $atividadeEugestor['tecnicos'];


            $tecnicoId = []; // Inicializa o array vazio

            foreach ($tecnicos as $tecnico) {
                $tecnicoId[] = $this->getPessoa($tecnico); // Adiciona cada técnico ao array
            }
            
            // Transforma o array em uma string separada por vírgulas
            $tecnicoIdString = implode(',', $tecnicoId);

           $dataToSave = [
            
            'project_id' =>$project_id,
            'start_time' => $datainicioFormatado,
            'end_time' => $datafimFormatado,
            'user_id' => $tecnicoIdString,
        ];

        $tempo_projeto->where('project_id', $project_id)->delete();

        $tempo_projeto->insert($dataToSave);

       
           
           }
       
           
          // Retorna os dados processados, se necessário
          return true;
  
      
  }

  public function GetContasOS()
  {
  

             
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
         $response = $this->client->get('https://sistema.eugestor.app/Financeiro/Contas.aspx', [
             'headers' => $headers,
         ]);
 
         // Obtém o HTML da resposta
         $html = $response->getBody()->getContents();

         $dom = new \DOMDocument();
        
         // Suprimir avisos de HTML malformado
         libxml_use_internal_errors(true);
         $dom->loadHTML($html);
         libxml_clear_errors();
         
         $xpath = new \DOMXPath($dom);
     
         // Captura todos os links que possuem "ContaPagarReceberId="
         $links = $xpath->query("//a[contains(@href, 'ContaPagarReceberId=')]");
     
         $contas = [];

         foreach ($links as $link) {
             $href = $link->getAttribute("href");
     
             // Extrair o ID da URL
             parse_str(parse_url($href, PHP_URL_QUERY), $queryParams);
             if (isset($queryParams['ContaPagarReceberId'])) {
                 $contaID = $queryParams['ContaPagarReceberId'];
                 $contas[] = $this->parseContaOS($contaID);
             }
         }


        $db = \Config\Database::connect();
        $despesas = $db->table('rise_expenses');

    

        foreach($contas as $conta){

            if (strlen($conta['numero_documento']) == 6) {
                // echo "Nota Fiscal encontrada! Encerrando o loop.\n";
                continue;  // Sai do loop imediatamente
             }
           
            $project_id = ltrim($conta['numero_documento'], '0'); 

            $valor = str_replace(['R$', ' '], '', $conta['valor_total']);
            $valor = str_replace('.', '', $valor); // Remove separador de milhar
            $valor = str_replace(',', '.', $valor); // Substitui a vírgula decimal por ponto
            $valor =  (float) $valor;
          
            
           

            // Converte para formato de banco de dados (YYYY-MM-DD)
            $data_ocorrencia = \DateTime::createFromFormat('d/m/Y', $conta['data_ocorrencia'])->format('Y-m-d');
        

            $dataToSave = [
                'id' => $conta['conta_id'],
                'category_id' => 1,
                'amount' => $valor,
                'title' => $conta['pessoa'],
                'project_id' => $project_id,
                'expense_date' => $data_ocorrencia,
            ];



            $existing = $despesas->where('id', $conta['conta_id'])->get()->getRow();

            if($existing){

                $despesas->where('id', $existing->id)->update($dataToSave);
            }else{
                $despesas->insert($dataToSave);
            }

        }

  
        echo 'Contas Atualizadas com sucesso';
         return true;
 
     
 }

 public function saveCliente($data, $type)
    {   
        set_time_limit(0);
        $dataUrl = 'https://api.eugestor.insidesistemas.com.br/api/pessoas/'.$type;



        //VERIFICA NUMERO DE PAGINAS
      
        $apiResponse = $this->requestDataPost($dataUrl, $data);

        return $apiResponse;

    
    }

    public function saveEnderecoCliente($idCliente, $data)
    {   
        set_time_limit(0);
        $dataUrl = 'https://api.eugestor.insidesistemas.com.br/api/pessoas/'.$idCliente.'/enderecos';



        //VERIFICA NUMERO DE PAGINAS
      
        $apiResponse = $this->requestDataPost($dataUrl, $data);

        return $apiResponse;

    
    }


 

 
   
//FUNÇÕES AUXILIARES

public function getProduto($produto)
{
 $produto = trim($produto);
 $produto = str_replace("\u{00A0}", " ", $produto); // Substitui espaços não-quebráveis
 $produto = str_replace(chr(160), " ", $produto); // Substitui caso esteja em outro encoding
 $produto = trim($produto); //Remove espaço antes e depois da variavel
 //$produto = 'CONDULETE MULTIPLO 1.1/2  T X COM TAMPA TRAMONTINA';
 $url = 'https://api.eugestor.insidesistemas.com.br/api/produtos?exibirInativos=false&pagina=1&tamanhoPagina=20&ordenarPor=produtoId&decrescente=true&termo='.$produto;

 $resposta = $this->requestData($url);



 if (isset($resposta['data']['lista']['0']['produtoId'])) {
    
     $produtoId =  ($resposta['data']['lista']['0']['produtoId']);

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
             return $record;
 }
 



}

public function getPessoa($tecnico)
{
 $tecnico = trim($tecnico);
 $tecnico = str_replace("\u{00A0}", " ", $tecnico); // Substitui espaços não-quebráveis
 $tecnico = str_replace(chr(160), " ", $tecnico); // Substitui caso esteja em outro encoding
 $tecnico = trim($tecnico); //Remove espaço antes e depois da variavel
 //$produto = 'CONDULETE MULTIPLO 1.1/2  T X COM TAMPA TRAMONTINA';
 $url = 'https://api.eugestor.insidesistemas.com.br/api/pessoas?termo='.$tecnico.'&apenasComContrato=false&exibirInativos=false&pagina=1&tamanhoPagina=20&decrescente=true';

 $resposta = $this->requestData($url);


 if (isset($resposta['data']['lista']['0']['pessoaId'])) {
    
     $tecnicoId =  ($resposta['data']['lista']['0']['pessoaId']);

             return $tecnicoId;
 }
 



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
                $colunaProduto = $colunas->item(0);
    
                // Remover spans antes de obter o texto
                foreach ($colunaProduto->getElementsByTagName("span") as $span) {
                    $span->parentNode->removeChild($span);
                }
    
                // Agora obtemos apenas o texto sem os spans
                $nomeProduto = trim($colunaProduto->textContent);
    
                $produto = [
                    'nome' => $nomeProduto,
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
    
    public function parseAtividadeFromHtml($htmlContent)
    {
        $dom = new \DOMDocument();
    
        // Suprimir avisos devido a HTML malformado
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();
    
        $xpath = new \DOMXPath($dom);
    
        // Seleciona todas as linhas da tabela
        $linhas = $xpath->query("//tbody/tr");
    
        $periodos = [];
        foreach ($linhas as $linha) {
            $colunas = $linha->getElementsByTagName("td");
    
            if ($colunas->length >= 3) { // Garantir que há colunas suficientes
                $data = trim($colunas->item(0)->textContent); // Data e hora combinadas
                $horaInicio = trim($colunas->item(1)->textContent); // Horário de início
                $horaFim = trim($colunas->item(2)->textContent); // Horário de fim
    
                // **Buscar técnicos dentro da mesma <tr>**
                $tecnicos = [];
                $tecnicosNodes = $xpath->query(".//ul/li", $linha); // Busca <li> dentro da <tr>
    
                foreach ($tecnicosNodes as $node) {
                    $tecnicos[] = trim($node->textContent);
                }
    
                // Separa data e horário corretamente
                preg_match('/(\d{2}\/\d{2}\/\d{4})\s*(\d{2}:\d{2})?/', $data, $matches);
                $dataSeparada = $matches[1] ?? $data;
                $horaInicioSeparada = $matches[2] ?? $horaInicio;
    
                // Corrigir horário final, removendo "até"
                if (strpos($data, "até") !== false) {
                    $horaFim = trim(str_replace("até", "", strstr($data, "até")));
                }
    
                // **Salvar os dados no array**
                $periodos[] = [
                    'data' => $dataSeparada,
                    'hora_inicio' => $horaInicioSeparada,
                    'hora_fim' => $horaFim,
                    'tecnicos' => !empty($tecnicos) ? $tecnicos : ["Nenhum técnico encontrado"],
                ];
            }
        }
    
        return $periodos;
    }
    
 

    public function parseContaOS($contaID)
    {
    
  
               
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
           $response = $this->client->get('https://sistema.eugestor.app/Financeiro/CadastroContas.aspx?ContaPagarReceberId='.$contaID, [
               'headers' => $headers,
           ]);
   
           // Obtém o HTML da resposta
           $html = $response->getBody()->getContents();

         

           $dom = new \DOMDocument();
        
           // Suprimir avisos de HTML malformado
           libxml_use_internal_errors(true);
           $dom->loadHTML($html);
           libxml_clear_errors();
           
           $xpath = new \DOMXPath($dom);

       
           // Expressões XPath para capturar os dados
           $dados = [
               'valor_total' => trim($xpath->evaluate("string(//div[contains(@class, 'Section__actions')]/span[1])")),
               'pessoa' => trim($xpath->evaluate("string(//label[contains(text(), 'Pessoa')]/following-sibling::a)")),
               'numero_documento' => trim($xpath->evaluate("string(//label[contains(text(), 'Nº do documento')]/following-sibling::text())")),
               'data_ocorrencia' => trim($xpath->evaluate("string(//label[contains(text(), 'Data de ocorrência')]/following-sibling::text())")),
               'descricao' => trim($xpath->evaluate("string(//label[contains(text(), 'Descrição')]/following-sibling::text())")),
               'conta_id'=> $contaID,
           ];
       
        return $dados;
   
       
   }

   public function dadosOS($id)
   {
   

              
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
          $response = $this->client->get('https://sistema.eugestor.app/Operacional/OrdemServico.aspx?OrdemServicoId='.$id.'&IsEditando=False&SubMenu=RE', [
              'headers' => $headers,
          ]);
  
          // Obtém o HTML da resposta
          $html = $response->getBody()->getContents();
  
          // Exibe o HTML diretamente (apenas para testes, remova em produção)

          $dom = new \DOMDocument();
            
          // Suprimir avisos devido a HTML malformado
          libxml_use_internal_errors(true);
          $dom->loadHTML($html);
          libxml_clear_errors();
      
          $xpath = new \DOMXPath($dom);

          $dados = [];

    // Extrai número da Ordem de Serviço
    $ordemServico = $xpath->evaluate("string(//div[contains(@class, 'Section__title') and contains(text(), 'Ordem de Serviço')])");
    preg_match('/\d+/', $ordemServico, $matches);
    $dados['ordem_servico'] = $matches[0] ?? '';

    // Extrai data de abertura
    $dataAberturaTexto = $xpath->evaluate("string(//div[label[contains(text(),'Data de Abertura:')]])");
    preg_match('/\d{2}\/\d{2}\/\d{4}/', $dataAberturaTexto, $matches);
    $dados['data_abertura'] = $matches[0] ?? '';

    // Extrai nome do cliente
    $cliente = $xpath->evaluate("string(//div[label[contains(text(),'Razão Social:')]]/text())");
    if (empty(trim($cliente))) {
        $cliente = $xpath->evaluate("string(//div[label[contains(text(),'Nome:')]]/text())");
    }

    $dados['cliente'] = trim($cliente);

    // Extrai status
    $status = $xpath->evaluate("string(//label[contains(text(),'Status:')]/following-sibling::div)");
    $dados['status'] = trim($status);

    // Extrai comentários
    $comentarios = [];
    $comentariosNodes = $xpath->query("//div[contains(@class, 'comentario-body')]");
    foreach ($comentariosNodes as $node) {
        $comentarios[] = trim($node->textContent);
    }

    $dados['comentarios'] = $comentarios;

         return $dados;

          
  
      
  }
    
    
    










}
