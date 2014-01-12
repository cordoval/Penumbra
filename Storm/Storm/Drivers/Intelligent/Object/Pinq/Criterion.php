<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Intelligent\Object\Closure;

class Criterion extends Object\Criterion {
    
    public function __construct($EntityType) {
        parent::__construct($EntityType);
    }
    
    public function AddPredicateClosure(Closure\IAST $AST) {
        $this->AddPredicate($this->ParseReturnExpression($AST));
    }
    
    public function AddOrderByClosure(Closure\IAST $AST, $Ascending) {
        $this->AddOrderByExpression($this->ParseReturnExpressionClosure($ExpressionClosure), $Ascending);
    }
    
    public function AddGroupByClosure(Closure\IAST $AST) {
        $this->AddGroupByExpression($this->ParseReturnExpressionClosure($ExpressionClosure));
    }
    
    private function ParseReturnExpression(Closure\IAST $AST) {
        if($AST->GetEntityMap()->GetEntityType() !== $this->GetEntityType()) {
            throw new \Exception('Closure must be for entity of type: ' . $this->GetEntityType());
        }        
        if(!$AST->HasReturnNode()) {
            throw new \Exception('Closure must contain a valid \'return\' statement for criterion');
        }
        $ReturnNodes = $AST->GetReturnNodes();
        if(count($ReturnNodes) > 1) {
            throw new \Exception('Closure must contain a single \'return\' statement for criterion');
        }
        
        $AST->SetPropertyMode(Closure\IAST::PropertiesAreGetters);
        
        return $AST->ParseNode($ReturnNodes[0]);
    }
}

?>
