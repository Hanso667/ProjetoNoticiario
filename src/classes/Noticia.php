<?php
class Noticia
{
    private $id = 0;
    private $autor = "";
    private $titulo = "";
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
    function setTitulo($titulo)
    {
        $this->titulo = $titulo;
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
    function getTitulo()
    {
        return $this->titulo;
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
