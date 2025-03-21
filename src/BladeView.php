<?php 

namespace JefyOkta\Blade;
use Oktaax\Intefaces\View;

class BladeView implements View{


    public function __construct(private $viewDir,private $cacheDir) {}

    public function render(string $view,array $data):?string {

        return (new Core($this->viewDir,$this->cacheDir))->render($view,$data);
    }
    

}
?>