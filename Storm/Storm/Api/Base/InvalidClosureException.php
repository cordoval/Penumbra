<?php

namespace Storm\Api\Base;

use \Storm\Drivers\Fluent\Object\Closure;

class InvalidClosureException extends \Storm\Core\StormException {
    /**
     * @var Closure\IData
     */
    private $ClosureData;
    
    public function __construct(Closure\IData $ClosureData, $Message) {
        $Reflection = $ClosureData->GetReflection();
        
        parent::__construct(null,
                'Invalid closure defined in %s:%d-%d: %s', 
                $Reflection->getFileName(),
                $Reflection->getStartLine(),
                $Reflection->getEndLine(),
                $Message);
        
        $this->ClosureData = $ClosureData;
    }
    
    final public function GetClosureData() {
        return $this->ClosureData;
    }
}

?>