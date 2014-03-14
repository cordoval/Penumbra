<?php

namespace Storm\Core\Object;

/**
 * The request represents a data to retrieved with rules in the domain model.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRequest {
    const IRequestType = __CLASS__;
    
    /**
     * @return string
     */
    public function GetEntityType();
        
    /**
     * @return ICriteria
     */
    public function GetCriteria();
    
    /**
     * @return boolean
     */
    public function HasSubEntityRequest();
    
    /**
     * @return IEntityRequest|null
     */
    public function GetSubEntityRequest();
    
    /**
     * Whether or not the criteria contains any group by expressions.
     * 
     * return boolean
     */
    public function IsGrouped();
    
    /**
     * @return Expression[]
     */
    public function GetGroupByExpressions();
    
    /**
     * Whether or not the criteria contains any group by expressions.
     * 
     * return boolean
     */
    public function IsAggregateConstrained();
    
    /**
     * @return Expression[]
     */
    public function GetAggregatePredicateExpressions();
}

?>