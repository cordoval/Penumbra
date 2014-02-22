<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object;

class DevelopmentProxyGenerator extends ProxyFileGenerator {
    private $ConcreteProxyDataGenerator;
    
    public function __construct($ProxyNamespace, $ProxyCachePath) {
        parent::__construct($ProxyNamespace, $ProxyCachePath);
        $this->ConcreteProxyDataGenerator = new ConcreteProxyDataGenerator();
    }
    
    public function GenerateProxies(Object\Domain $Domain, $EntityType, 
            array $AlreadyKnownRevivalDataArray,
            array $RevivalDataLoaderFunctions) {
        $EntityReflection = new \ReflectionClass($EntityType);
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        $Proxies = [];
        foreach($RevivalDataLoaderFunctions as $Key => $RevivalDataLoaderFunction) {
            $Proxies[] = $this->GenerateProxyInstance($Domain, $EntityReflection, $ProxyClassName, $FullProxyName, 
                    $AlreadyKnownRevivalDataArray[$Key],
                    $RevivalDataLoaderFunction);
        }
        
        return $Proxies;
    }

    public function GenerateProxy(Object\Domain $Domain, $EntityType, 
            Object\RevivalData $AlreadyKnownRevivalData,
            callable $RevivalDataLoaderFunction) {
        $EntityReflection = new \ReflectionClass($EntityType);
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        return $this->GenerateProxyInstance($Domain, $EntityReflection, $ProxyClassName, $FullProxyName, 
                $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
    }
    
    private function GenerateProxyInstance(Object\Domain $Domain, $EntityReflection, $ProxyClassName, $FullProxyName, 
            Object\RevivalData $AlreadyKnownRevivalData,
            callable $RevivalDataLoaderFunction) {
        if(class_exists($FullProxyName, false)) {
            return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
        }
        else {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            
            $this->GenerateProxyClassFile($ProxyFileName, $ProxyClassName, $EntityReflection);
            
            require $ProxyFileName;
            return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
        }
    }
    
    private function GenerateProxyClassFile($ProxyFileName, $ProxyClassName, \ReflectionClass $EntityReflection) {
        $ProxyClassTemplate = $this->ConcreteProxyDataGenerator->GenerateConcreteProxyData($this->ProxyNamespace, $ProxyClassName, $EntityReflection);
        $this->GenerateProxyFile($ProxyFileName, $ProxyClassTemplate);
    }

    private function GenerateProxyFile($ProxyFileName, $Template) {
        $DirectoryPath = pathinfo($ProxyFileName, PATHINFO_DIRNAME);
        if (!file_exists($DirectoryPath)) {
            mkdir($DirectoryPath, 0777, true);
        }
        file_put_contents($ProxyFileName, $Template);
    }
}

?>