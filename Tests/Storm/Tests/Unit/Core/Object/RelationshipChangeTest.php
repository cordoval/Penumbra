<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Tests\StormTestCase;
use Storm\Core\Object\RelationshipChange;

class RelationshipChangeTest extends StormTestCase {
    private $PersistedRelationship;
    private $DiscardedRelationship;
    
    protected function setUp() {
        $this->PersistedRelationship = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
        $this->DiscardedRelationship = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
    }
    
    public function testSuppliedParametersAreEqualToGetterMethods() {
        
        $RelationshipChange = new RelationshipChange($this->PersistedRelationship, $this->DiscardedRelationship);
        
        $this->assertEquals($RelationshipChange->GetPersistedEntityData(), $this->PersistedRelationship);
        $this->assertEquals($RelationshipChange->GetDiscardedIdentity(), $this->DiscardedRelationship);
    }
    
    public function testOnlyHasPersistedRelationship() {
        $RelationshipChange = new RelationshipChange($this->PersistedRelationship, null);
        
        $this->assertTrue($RelationshipChange->HasPersistedEntityData());
        $this->assertFalse($RelationshipChange->HasDiscardedIdentity());
    }
    
    public function testOnlyHasDiscardedRelationship() {
        $RelationshipChange = new RelationshipChange(null, $this->DiscardedRelationship);
        
        $this->assertTrue($RelationshipChange->HasDiscardedIdentity());
        $this->assertFalse($RelationshipChange->HasPersistedEntityData());
    }
}

?>