<?php
class Noticia
{
    private $id = 0;
    private $autor = "";
    private $conteudo = "";
    private $data = "";

    function setId($id)
    {
        $this->id = $id;
    }
    function setAutor($autor)
    {
        $this->autor = $autor;
    }
    function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
    }
    function setData($data)
    {
        $this->data = $data;
    }

    function getId()
    {
        return $this->id;
    }
    function getAutor()
    {
        return $this->autor;
    }
    function getConteudo()
    {
        return $this->conteudo;
    }
    function GetData()
    {
        return $this->data;
    }
}
