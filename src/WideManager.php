<?php

class WideManager {

	private $dominio = null;
	private $autenticacao = array();

	public $requisicoes = array();

	public function __construct($dominio, $email, $token) {

		$this->dominio = $dominio;
		$this->autenticacao = array(
			'email' => $email,
			'token' => $token
		);

	}

	public function api($local, $parametros = array()) {

		if (!$this->dominio) {

			$requisicao = array(
				'success' => false,
				'error' => 'É necessário informar o domínio para efetuar a comunicação.'
			);

		} else if (!$this->autenticacao['email'] || !$this->autenticacao['token']) {

			$requisicao = array(
				'success' => false,
				'error' => 'É necessário informar o e-mail e o token para efetuar a autenticação.'
			);

		} else {

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'http://' . $this->dominio . '/api/' . trim($local, '/'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json')); 
			curl_setopt($curl, CURLOPT_USERPWD, $this->autenticacao['email'] . ':' . $this->autenticacao['token']);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parametros));
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			$exec = curl_exec($curl);
			curl_close($curl);

			if ($exec) {

				$requisicao = json_decode($exec, true);

				if (!is_array($requisicao)) {

					$requisicao = array(
						'success' => false,
						'error' => 'Não foi possível tratar o retorno.'
					);

				}

			} else {

				$requisicao = array(
					'success' => false,
					'error' => 'Sem comunicação com o servidor.'
				);

			}

		}

		$this->requisicoes[] = (object) $requisicao;

		return end($this->requisicoes);

	}

}