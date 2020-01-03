<?php

class WideManager {

    private $dominio;
    private $token;

    public $requisicoes = array();

    public function __construct($dominio, $token = null) {

        $this->dominio = $dominio;
        $this->token = $token;

    }

    public function api($local, $parametros = array()) {

        if (!$this->dominio) {

            $requisicao = array(
                'sucesso' => false,
                'erro' => 'É necessário informar o domínio para efetuar a comunicação.'
            );

        } else {

            $header = array(
                'Accept: application/json'
            );
            
            if ($this->token) {
                $header[] = 'Authorization: Bearer ' . $this->token;
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'http://' . $this->dominio . '/api/' . trim($local, '/'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parametros));
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            $exec = curl_exec($curl);
            curl_close($curl);

            if ($exec) {

                $requisicao = json_decode($exec, true);

                if (!is_array($requisicao)) {

                    $requisicao = array(
                        'sucesso' => false,
                        'erro' => 'Não foi possível tratar o retorno.',
                    );

                    if ($exec) {
                        $requisicao['retorno'] = $exec;
                    }

                }

            } else {

                $requisicao = array(
                    'sucesso' => false,
                    'erro' => 'Sem comunicação com o servidor.'
                );

            }

        }

        $this->requisicoes[] = (object) $requisicao;

        return end($this->requisicoes);

    }

}
