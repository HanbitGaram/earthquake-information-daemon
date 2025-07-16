<?php
namespace Library;
// https://gist.github.com/HanbitGaram/cc8b84cdea55f8c3233a6a388efddbe2
class File{
    protected ?string $saveFunctions = null;
    protected ?string $loadFunctions = null;

    public function __construct(){
        /***
         * file_***_contents 는 fopen을 랩핑한 것이지만,
         * 서버에 따라서 보안상 실행을 차단하여 사용이 안되는 경우가 은근히 존재함.
         * 설정을 따로 두는 이유는, 혹시 모를 get만 차단했다던가... 등의 사유
         */
        $this->saveFunctions = (!function_exists('file_put_contents')) ? 'fopen' : 'file';
        $this->loadFunctions = (!function_exists('file_get_contents')) ? 'fopen' : 'file';
    }
    public function save(string $filePath, string $fileContents): string|bool{
        return $this->_save($filePath, $fileContents);
    }
    public function load(string $filePath): string|bool{
        return $this->_load($filePath);
    }
    public function fpassthru(string $filePath): string|bool{
        return $this->_load($filePath, 'passthru');
    }
    protected function _save(string $filePath, string $fileContents): string|bool{
        try{
            if($this->saveFunctions==='fopen'){
                $fp = fopen($filePath, 'wb');
                fwrite($fp, $fileContents);
                fclose($fp);
                return true;
            }else if($this->saveFunctions==='file')
                return !!file_put_contents($filePath, $fileContents);
            else throw new Exception("Save function is not defined");
        }catch(\Throwable $error){
            $error = $error->getMessage();
            // 처리가 필요하면 작성하면 될듯함.
            return false;
        }
    }
    // 메모리 이슈가 존재할 수 있긴 한데, 파일을 단순하게 뿌려주는거면 fpassthru 가 좋기는 함.
    protected function _load(string $filePath, ?string $option=null): string|bool{
        try{
            if(!is_file($filePath)) throw new Exception("File is not defined");
            if($option==='passthru'){
                $fp = fopen($filePath, 'rb');
                fpassthru($fp);
                fclose($fp);
                return true;
            }else if($this->saveFunctions==='fopen'){
                $fp = fopen($filePath, 'rb');
                $fileData = fread($fp, filesize($filePath));
                fclose($fp);
                return $fileData;
            }else if($this->saveFunctions==='file')
                return file_get_contents($filePath);
            else if(function_exists('readfile')){
                ob_start();
                readfile($filePath);
                $fileData = ob_get_contents();
                ob_end_clean();
                return $fileData;
            }
            else throw new \Exception("Load function is not defined");

        }catch(\Throwable $error){
            $error = $error->getMessage();
            // 처리가 필요하면 작성하면 될듯함.
            return false;
        }
    }
}
