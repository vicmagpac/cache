<?php

/**
* Sistema de cache
*/
class Cache
{
	/**
	* Tempo padrão do Cache
	* @var String
	*/
	private static $time = '5 minutes';

	/**
	* Local onde o cache será salvo
	* Definido pelo construtor
	* @var String
	*/
	private $folder;

	/**
	* Construtor
	* Inicializa a classe e permite a definição de onde os arquivos serão salvos.
	* Se o parametro $folder for ignorado o local dos arquivos temporários do sistema operacional será usado
	* @param string $folder Local para salvar os arquivos de cache (opcional)
	* @return void
	*/
	public function __construct($folder = NULL)
	{
		$this->setFolder(!is_null($folder) ? $folder : sys_get_temp_dir());
	}

	/**
	* Define onde o arquivo de cache será salvo
	* Irá verificar se a pasta existe e pode ser escrita, caso contrario uma mensagem de erro será exibida
	* @param String $folder Local para salvar os arquivos de cache(opcional)
	* @return void
	*/
	protected function setFolder($folder)
	{
		//se a pasta existir, for uma pasta e puder ser escrita
		if(file_exists($folder) && is_dir($folder) && is_writable($folder)) {
			$this->folder = $folder;
		}else {
			trigger_error('Não foi possivel acessar a pasta de cache', U_USER_ERROR);
		}
	}

	/**
	* Gera o nome do arquivo de cache baseado na chave passa
	* @param String $key uma chave para identificar o arquivo
	* @return String local onde do arquivo de cache
	*/
	protected function generateFileLocation($key)
	{
		return $this->folder . DIRECTORY_SEPARATOR . sha1($key) . '.tmp';
	}

	/**
	* Cria um arquivo de cache
	* @param String $key Uma chave para identificar o arquivo
	* @param String $content Conteúdo do arquivo de cache
	* @return boolean Se o arquivo foi criado
	*/
	protected function createCacheFile($key, $content)
	{
		//gera o nome do arquivo
		$filename = $this->generateFileLocation($key);

		//cria o arquivo com o conteúdo
		return file_put_contents($filename, $content) OR trigger_error('Não foi possivel criar o arquivo de cache', U_USER_ERROR);
	}

	/**
	* Salva um valor no cache
	* @param String $key Uma chave para identificar o valor cacheado
	* @param mixed $content Conteudo/variavel a ser salvo no cache
	* @param String $time Quanto tempo até o cache expirar (opcional)
	* @return boolean se o cache foi salvo
	*/
	public function save($key, $content, $time)
	{
		$time = strtotime(!is_null($time) ? $time : self::time);
		$content = serialize(
						array(
							'expires' => $time,
							'content' => $content
 						)
					);
		return $this->createCacheFile($key, $content);
	}

	/**
	* Ler o valor no cache
	* @param String $key Uma chave para identificar o valor cacheado
	* @return mixed Se o cache foi encontrado retorna o seu valor, caso contrário NULL
	*/
	public function read($key)
	{
		$filename = $this->generateFileLocation($key);
		if(file_exists($filename) && is_readable($filename)) {
			$cache = unserialize(file_get_contents($filename));
			if($cache['expires'] > time()) {
				return $cache['content'];
			}else {
				unlink($filename);
			}
		}
		return null;
	}

}