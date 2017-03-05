<?php

namespace App\Model;

use PHPMailer;
use Comodojo\Cookies\Cookie;
use Websoftwares\Session;


class Rotina implements IRotina
{
    const DADOS_EMAIL = [
        'debug' => 0,
        'auth' => true,
        'secure' => 'tls',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '',
        'password' => '',
        'fromEmail' => '',
        'fromUser' => '',
        'assunto' => ''
    ];

    /**
     * Esse método é responsavel por extrair o numero de um endereço
     * @param string $endereco
     * @return string
     */
    public function extrairNumeroEndereco(string $endereco): string
    {
        preg_match_all('!\d+(?:\.\d+)?!', $endereco, $matches);
        return (string) current(array_map('floatval', current($matches)));
    }

    /**
     * Método responsavel por enviar um email
     * @param string $email
     * @param string $token
     * @return bool
     * @throws \Exception
     */
    public function sendEmail(string $email, string $token): bool
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = self::DADOS_EMAIL['debug'];
        $mail->SMTPAuth = self::DADOS_EMAIL['auth'];
        $mail->SMTPSecure = self::DADOS_EMAIL['secure'];
        $mail->Host = self::DADOS_EMAIL['host'];
        $mail->Port = self::DADOS_EMAIL['port'];
        $mail->Username = self::DADOS_EMAIL['username'];
        $mail->Password = self::DADOS_EMAIL['password'];
        $mail->SetFrom(self::DADOS_EMAIL['fromEmail'], self::DADOS_EMAIL['fromUser']);
        $mail->Subject = self::DADOS_EMAIL['assunto'];
        $mail->Body = ' token : ' . $token;
        $mail->AddAddress($email);
        try {
            return $mail->Send();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * gerar um token unico
     * @return string
     */
    public function gerarToken(): string
    {
        return (string) md5(uniqid(rand(), true));
    }

    /**
     * Grava o token em um novo coockie
     * @param string $token
     * @return bool
     */
    public function gravarCookie(string $token): bool
    {
        $cookie = new Cookie('fullStack');
        $result = $cookie->setValue($token)
            ->setExpire( time()+3600 )
            ->save();

        return $result;
    }

    /**
     * Grava o token em uma nova sessão
     * @param string $token
     * @return bool
     */
    public function gravarSession(string $token)
    {
        $session = new Session;
        $session->destroy();
        $session->start();
        $session["fullStack"] = ['token' => $token];
    }

    /**
     * Retorna o ip do cliente
     * @return string
     */
    public function getIp(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /**
     * Método responsavel por validar e limpar os dados
     * @param array $dados
     * @return array
     * @throws \Exception
     */
    public function validacao(array $dados): array
    {
        if (empty($dados['email'])) {
            throw new \Exception('Nenhum e-mail foi enviado');
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('E-mail enviado não é válido');
        }

        $retorno['email'] = filter_var($dados['email'], FILTER_SANITIZE_EMAIL);
        $retorno['endereco'] = filter_var($dados['endereco'], FILTER_SANITIZE_STRING);

        if (empty($retorno['endereco'])) {
            throw new \Exception('Endereço enviado não é válido!');
        }

        return $retorno;
    }
}