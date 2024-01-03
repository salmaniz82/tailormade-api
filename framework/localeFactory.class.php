<?php final class LocaleFactory
{
    
	public $lang = null;
	public $def = null;

    private function __construct()
    {
        global $langdef;
        $this->def = $langdef;

        if(isset($_SESSION['lang']))
        {
            $this->lang = $_SESSION['lang'];
        }
        else {

            $this->lang = 'en';
            $_SESSION['lang'] = $this->lang;
        }

    }




    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new LocaleFactory();
        }
        return $inst;
    }

    public function registerLangMutate()
    {
        $_SESSION['lang'] = $this->lang;
    }

    

    public function setLang($var = null)
    {
    	$this->lang = ($var != null) ? $var : 'en';
        $this->registerLangMutate();
    }


    public function switchLang()
    {
    	if($this->lang == 'en')
        {
            $this->setLang('ar');       
        }
        else if($this->lang == 'ar')
        {
            $this->setLang('en');
        }

        $this->registerLangMutate();
    }

    public function df($varKey)
    {
        $langkey = $this->langIndex();
    	return $this->def[$varKey][$langkey];
    }


    public function deco($varKey)
    {

    	echo $this->df($varKey);

    }

    public function langIndex()
    {
        if($this->lang == 'en') 
        {
            $langIndex = 0;        
        }

        else if ($this->lang == 'ar')
        {
            $langIndex = 1;   
        }

        else {
            $langIndex = null;
        }

        return $langIndex;
    }



}



