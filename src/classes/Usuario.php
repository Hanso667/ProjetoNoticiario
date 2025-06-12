<?php
class Usuario
{
    private $id = 0;
    private $nome = "";
    private $email = "";
    private $senha = "";

    function setId($id)
    {
        $this->id = $id;
    }
    function setNome($nome)
    {
        $this->nome = $nome;
    }
    function setEmail($email)
    {
        $this->email = $email;
    }
    function setSenha($senha)
    {
        $this->senha = $senha;
    }

    function GetId($id)
    {
        return $this->id;
    }
    function GetNome($nome)
    {
        return $this->nome;
    }
    function GetEmail($email)
    {
        return $this->email;
    }
    function GetSenha($senha)
    {
        return $this->senha;
    }
}
