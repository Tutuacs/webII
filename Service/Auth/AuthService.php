<?php

class AuthService
{
    private $usuarioDao;

    public function __construct(DaoFactory $factory)
    {
        $this->usuarioDao = $factory->getUsuarioDao();
    }

    public function autenticar($login, $senha)
    {
        $usuario = $this->usuarioDao->buscaPorLogin($login);
        if (!$usuario) {
            return null;
        }

        if (md5($senha) !== $usuario->getSenha()) {
            return null;
        }

        return $usuario;
    }

    public function iniciarSessao(Usuario $usuario)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nome_usuario'] = $usuario->getNome();
        $_SESSION['login_usuario'] = $usuario->getLogin();
    }

    public function encerrarSessao()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}
