<?php

namespace App\Model;

interface IRotina
{
    /**
     * Esse método é responsavel por extrair o numero de um endereço
     * @param string $endereco
     * @return string
     */
    public function extrairNumeroEndereco(string $endereco) : string;

    /**
     * Método responsavel por enviar um email
     * @param string $email
     * @param string $token
     * @return bool
     */
    public function sendEmail(string $email, string $token) : bool;

    /**
     * gerar um token unico
     * @return string
     */
    public function gerarToken() : string ;

    /**
     * Grava o token em um novo coockie
     * @param string $token
     * @return bool
     */
    public function gravarCookie(string $token) : bool ;

    /**
     * Grava o token em uma nova sessão
     * @param string $token
     * @return mixed
     */
    public function gravarSession(string $token);

    /**
     * Retorna o ip do cliente
     * @return string
     */
    public function getIp() : string ;

    /**
     * Método responsavel por validar e limpar os dados
     * @param array $dados
     * @return array
     * @throws \Exception
     */
    public function validacao(array $dados) : array ;

}