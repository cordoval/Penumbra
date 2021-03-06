<?php

namespace Penumbra\Drivers\Base\Mapping\Queries;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Mapping\ExpressionMapper;

class CriteriaMapper {
    
    protected function MapCriteria(
            Object\ICriteria $Criteria, 
            Relational\Criteria $RelationalCriteria,
            ExpressionMapper $ExpressionMapper) {
        if($RelationalCriteria === null) {
            $RelationalCriteria = $this->GetSelectCriteria($Criteria->GetEntityType());
        }
        
        if ($Criteria->IsConstrained()) {
            foreach ($ExpressionMapper->MapAll($Criteria->GetPredicateExpressions()) as $PredicateExpression) {
                $RelationalCriteria->AddPredicateExpression($PredicateExpression);
            }
        }
        
        if ($Criteria->IsOrdered()) {
            $ExpressionAscendingMap = $Criteria->GetOrderByExpressionsAscendingMap();
            
            foreach ($ExpressionAscendingMap as $Expression) {
                $IsAscending = $ExpressionAscendingMap[$Expression];
                $RelationalCriteria->AddOrderByExpression($ExpressionMapper->Map($Expression), $IsAscending);
            }
        }
        
        if ($Criteria->IsRanged()) {
            $RelationalCriteria->SetRangeOffset($Criteria->GetRangeOffset());
            $RelationalCriteria->SetRangeAmount($Criteria->GetRangeAmount());
        }
        
        return $RelationalCriteria;
    }        
}

?>