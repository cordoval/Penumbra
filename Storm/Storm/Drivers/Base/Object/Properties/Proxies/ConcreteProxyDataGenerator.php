<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object;

/**
 * Used to determine the members of the proxy functionality
 */
class Null__Proxy implements IProxy {
    use \Storm\Core\Helpers\Type;
    use EntityProxyFunctionality;
    
    
    public function __construct() {
        return call_user_func_array([$this, '__ConstructProxy'], func_get_args());
    }
}

class ConcreteProxyDataGenerator {
    const ProxyTemplate = <<<'NOW'
<?php

/**
 * Proxy Class for <EntityClass> auto-generated by Storm.
 *                  --DO NOT MODIFY--
 */

namespace <Namespace>;

use <ProxyInterface> as IProxy;
use <ProxyFunctionality> as ProxyFunctionality;

class <ProxyName> extends <EntityClass> implements IProxy {
    use ProxyFunctionality;
    
    public function __construct() {
        return call_user_func_array([$this, '__ConstructProxy'], func_get_args());
    }

    <OveriddenMethods>
}

?>
NOW;
    
    const OverriddenMethodTemplate = <<<'NOW'
   
    <Modifiers> <Name> (<Parameters>) {
        $this->__Load();
        return parent::<Name>(<ParameterVariables>);
    }
NOW;
    private static $NullProxyReflection;
    private static $NullProxyProperties = [];
    private static $NullProxyMethods = [];

    public function __construct() {
        if(!isset(self::$NullProxyReflection)) {
            self::$NullProxyReflection = new \ReflectionClass(Null__Proxy::GetType());
            foreach(self::$NullProxyReflection->getProperties() as $Property) {
                self::$NullProxyProperties[$Property->getName()] = $Property;
            }
            foreach(self::$NullProxyReflection->getMethods() as $Method) {
                self::$NullProxyMethods[$Method->getName()] = $Method;
            }
        }
    }

    public function GenerateConcreteProxyData($ProxyNamespace, $ProxyClassName, \ReflectionClass $EntityReflection) {
        $ProxyTemplate = self::ProxyTemplate;

        $EntityClass = '\\' . $EntityReflection->getName();
        
        /**
         * Not used as all entity properties are unset and
         * will load when __get/__set is triggered.
         */
        $OverridenMethods = '';// $this->GenerateOverridingMethods($EntityReflection);
        
        $Replacements = [
                    '<ProxyInterface>' => IProxy::IProxyType,
                    '<ProxyFunctionality>' => __NAMESPACE__ . '\\EntityProxyFunctionality',
                    '<Namespace>' => $ProxyNamespace,
                    '<ProxyName>' => $ProxyClassName,
                    '<EntityClass>' => $EntityClass,
                    '<OveriddenMethods>' => $OverridenMethods,
                ];
        
        return str_replace(array_keys($Replacements), $Replacements, $ProxyTemplate);
    }
    
    private function GenerateOverridingMethods(\ReflectionClass $EntityReflection) {
        $OverridenMethods = [];
        foreach($EntityReflection->getMethods() as $Method) {
            if(isset(self::$NullProxyMethods[$Method->getName()])
                    || !$Method->isPublic()
                    || $Method->isStatic()
                    || $Method->isFinal())
                continue;
            else {
                $OverridenMethods[] = $this->GenerateOverridingMethodTemplate($Method);
            }
        }
        
        return implode(PHP_EOL, $OverridenMethods);
    }

    private function GenerateOverridingMethodTemplate(\ReflectionMethod $EntityMethod) {
        $MethodTemplate = self::OverriddenMethodTemplate;

        $Modifiers = \Reflection::getModifierNames($EntityMethod->getModifiers());
        $Modifiers[] = 'function';
        if($EntityMethod->returnsReference())
            $Modifiers[] = '&';
        $Modifiers = implode(' ', $Modifiers);

        $Name = $EntityMethod->getName();

        $Parameters = [];
        $ParameterVariables = [];
        foreach($EntityMethod->getParameters() as $Parameter) {
            $ParameterVariables[] = '$' . $Parameter->getName();
            $Parameters[] = $this->GenerateMethodParameter($Parameter);
        }
        $Parameters = implode(', ', $Parameters);
        $ParameterVariables = implode(', ', $ParameterVariables);

        $MethodTemplate = str_replace('<Modifiers>', $Modifiers, $MethodTemplate);
        $MethodTemplate = str_replace('<Name>', $Name, $MethodTemplate);
        $MethodTemplate = str_replace('<Parameters>', $Parameters, $MethodTemplate);
        $MethodTemplate = str_replace('<ParameterVariables>', $ParameterVariables, $MethodTemplate);

        return $MethodTemplate;
    }

    private function GenerateMethodParameter(\ReflectionParameter $MethodParameter) {
        $TypeHint = '';
        if($MethodParameter->isArray())
            $TypeHint = 'array';
        else if($MethodParameter->isCallable())
            $TypeHint = 'callable';
        else {
            if($MethodParameter->getClass() !== null)
                $TypeHint = '\\' . $MethodParameter->getClass()->getName();
        }
        $Reference = $MethodParameter->isPassedByReference() ? '&' : '';
        $VariableName = '$' . $MethodParameter->getName();
        $DefaultValue = '';
        if($MethodParameter->isDefaultValueAvailable()) {
            $DefaultValue .= '= '; 
            /**
             *  -- CANT USE DUE TO COMPATIBILITY WITH PHP 5.4 -- 
             *  if($MethodParameter->isDefaultValueConstant()) 
             *      $DefaultValue .= '\\' . $MethodParameter->getDefaultValueConstantName();
             *  else
             */
            $DefaultValue .= var_export($MethodParameter->getDefaultValue(), true);
        }

        return implode(' ', array_filter([$TypeHint, $Reference, $VariableName, $DefaultValue]));
    }
}

?>