<?php 

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog;

use \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities;
use \Penumbra\Api;
use \Penumbra\Api\Base\ORM;
use \Penumbra\Api\Base\EntityManager;
use \Penumbra\Drivers\Platforms;
use \Penumbra\Drivers\Platforms\Development\Logging;
use \Penumbra\Drivers\Base\Object\Properties\Proxies;

class One implements \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\IPenumbraExample {
    const DevelopmentMode = 1;
    const UseCache = false;
    
    const Id = 500;
    
    const Persist = 0;
    const Retreive = 1;
    const RetreiveComplex = 2;
    const PersistExisting = 3;
    const Discard = 4;
    const Procedure = 5;
    
    public function Run(ORM $BloggingPenumbra) {
        $BlogRepository = $BloggingPenumbra->GetEntityManger(Entities\Blog::GetType());
        $TagRepository = $BloggingPenumbra->GetEntityManger(Entities\Tag::GetType());
        $AuthorRepository = $BloggingPenumbra->GetEntityManger(Entities\Author::GetType());
        
        $Action = self::Retreive;
        
        $Amount = 1;        
        $Last;
        for ($Count = 0; $Count < $Amount; $Count++) {
            $Last = $this->Act($Action, $BloggingPenumbra, $BlogRepository, $AuthorRepository, $TagRepository);
        }

        return $Last;
    }

    private function Act($Action, ORM $BloggingPenumbra, EntityManager $BlogRepository, EntityManager $AuthorRepository, EntityManager $TagRepository) {
        $Id = self::Id;
        switch ($Action) {
            case self::Persist:
                return $this->Persist($Id, $BloggingPenumbra, $BlogRepository, $AuthorRepository, $TagRepository);


            case self::Retreive:
                return $this->Retreive($Id, $BloggingPenumbra, $BlogRepository, $TagRepository);


            case self::RetreiveComplex:
                return $this->RetreiveComplex($Id, $BloggingPenumbra, $BlogRepository, $TagRepository);

                
            case self::PersistExisting:
                return $this->PersistExisting($Id, $BloggingPenumbra, $BlogRepository, $TagRepository);

                
            case self::Procedure:
                return $this->Procedure($Id, $BloggingPenumbra, $BlogRepository, $TagRepository);


            case self::Discard:
                return $this->Discard($Id, $BloggingPenumbra, $BlogRepository, $TagRepository);

            default:
                return null;
        }
    }
    
    private function Persist($Id, ORM $BloggingPenumbra, 
            EntityManager $BlogRepository, 
            EntityManager $AuthorRepository,
            EntityManager $TagRepository) {
        
        $Blog = $this->CreateBlog();
        foreach ($Blog->Posts as $Post) {
            $TagRepository->PersistAll($Post->Tags->getArrayCopy());
            $AuthorRepository->Persist($Post->Author);
        }
        $BloggingPenumbra->SaveChanges();

        $BlogRepository->Persist($Blog);
        $BlogRepository->SaveChanges();

        return $Blog;
    }
    
    private function Retreive($Id, ORM $BloggingPenumbra, EntityManager $BlogRepository, EntityManager $TagRepository) {
        $RevivedBlog = $BlogRepository->LoadById($Id);
        if($RevivedBlog === null) {
            throw new \Exception("Entity with id: $Id does not exist");
        }
        if(extension_loaded('xdebug')) {
            var_dump($RevivedBlog);
        }
        $Post = $RevivedBlog->Posts[0];
        $Author = $Post->Author;
        $Test = $Author->FirstName;
        $Foo = $RevivedBlog->Posts[1]->Tags->getArrayCopy();
        $BlogRepository->GetIdentityMap()->Clear();
        
        return null;
    }
    
    private function RetreiveComplex($Id, ORM $BloggingPenumbra, EntityManager $BlogRepository, EntityManager $TagRepository) {
        $Outside = new \DateTime();
        $Outside->sub(new \DateInterval('P1D'));

        $Array = [1,2,3,4,5,6];
        $RevivedBlog = $BlogRepository->Load($BlogRepository->Request()
                ->Where(function ($Blog) use($Id, $Outside, $Array) {
                    $Foo = $Id;
                    $Sandy = 40;
                    $Sandy += $Id;

                    $ADate = new \DateTime();

                    $Awaited = $ADate->add(new \DateInterval('P2Y1DT15M')) > new \DateTime() || 
                            acos(atan(tan(sin(pi()))));

                    $True = null === null && null !== false || false !== true && in_array(1, $Array);

                    $Possibly = $Foo . 'Hello' <> ';' || $Sandy == time() && $Outside->getTimestamp() > (time() - 3601);

                    $Maybe = $Blog->Description != 45 || (~3 - 231 * 77) . $Blog->GetName() == 'Sandwich' && $True || $Awaited;
                    
                    return (~1 - 500 ^ 2) && $Foo === $Blog->Id && (true || mt_rand(1, 10) > 10 || $Blog->Id === $Foo  || $Blog->CreatedDate < new \DateTime() && $Maybe || $Possibly);
                })
                ->OrderBy(function ($Blog) { return $Blog->Id . $Blog->CreatedDate; })
                ->OrderByDescending(function ($Blog) { return $Blog->Id; })
                ->GroupBy(function ($Blog) { return $Blog->Id; })
                ->First());
        
        if($RevivedBlog === null) {
            throw new \Exception("Entity with id: $Id does not exist");
        }
        if(extension_loaded('xdebug')) {
            var_dump($RevivedBlog);
        }
        $RevivedBlog->Posts[0]->Tags->getArrayCopy();
        $RevivedBlog->Posts[1]->Tags->getArrayCopy();
        $BlogRepository->GetIdentityMap()->Clear();

        return null;
    }
    
    private function PersistExisting($Id, ORM $BloggingPenumbra, EntityManager $BlogRepository, EntityManager $TagRepository) {
        
        $Blog = $BlogRepository->LoadById($Id);
        $Blog->Posts[0]->Content = 'foobar';
        $Blog->Posts[0]->Author->FirstName .= 'a';
        $Blog->Posts[1]->Content = 'BarBar---------------!';
        
        $BlogRepository->Persist($Blog);
        $BlogRepository->SaveChanges();

        return $Blog;
    }
    
    private function Procedure($Id, ORM $BloggingPenumbra, EntityManager $BlogRepository, EntityManager $TagRepository) {
        $Procedure = $BlogRepository->Procedure([$this, 'UpdateBlog'])
                ->Where(function ($Blog) use ($Id) {
                    return $Blog->Id === $Id && null == null && (~3 ^ 2) < (40 % 5) && in_array(1, [1,2,3,4,5,6]);
                }); 

        $BlogRepository->Execute($Procedure);

        $BlogRepository->SaveChanges();
    }
    
    public function UpdateBlog(Entities\Blog $Blog) {
        $Blog->Description = hash('sha1', $Blog->GetName());

        $Blog->SetName(substr($Blog->GetName() . (strpos($Blog->Description, 'Test') !== false ?
                'Foobar' . (string)$Blog->CreatedDate : $Blog->GetName() . 'Hi'), 0, 50));

        $Blog->CreatedDate = (new \DateTime())->add((new \DateTime())->diff($Blog->CreatedDate, true));
    }
    
    private function Discard($Id, ORM $BloggingPenumbra, EntityManager $BlogRepository, EntityManager $TagRepository) {

        $BlogRepository->Discard($BlogRepository->LoadById($Id));

        $BlogRepository->SaveChanges();
    }
    
    
    private function CreateBlog() {
        $Blog = new Entities\Blog();
        $Blog->Name = 'Test blog';
        $Blog->Description = 'The tested blog';
        $Blog->CreatedDate = new \DateTime();
        $Blog->Posts = new \ArrayObject([]);
        $this->CreatePosts($Blog);

        return $Blog;
    }

    private function CreateAuthor() {
        $FirstNames = ['Joe', 'Jack', 'Bill', 'Tom', 'Sandy', 'Mat'];
        $LastNames = ['Runt', 'Paffy', 'Derka', 'Shammy', 'Tuple', 'White'];
        
        $Author = new Entities\Author();
        $Author->FirstName = $FirstNames[rand(0, count($FirstNames) - 1)];
        $Author->LastName = $LastNames[rand(0, count($LastNames) - 1)];
        
        return $Author;
    }

    private function CreatePosts(Entities\Blog $Blog) {
        $Post1 = new Entities\Post();
        $Post1->Blog = $Blog;
        $Post1->Author = $this->CreateAuthor();
        $Post1->Title = 'Hello World';
        $Post1->Content = 'What\'s up?';
        $Post1->CreatedDate = new \DateTime();
        $Post1->Tags = new \ArrayObject([]);
        $this->AddTags($Post1);
        $Blog->Posts[] = $Post1;

        $Post2 = new Entities\Post();
        $Post2->Blog = $Blog;
        $Post2->Author = $this->CreateAuthor();
        $Post2->Title = 'Hello Neptune';
        $Post2->Content = 'What\'s going on nup?';
        $Post2->CreatedDate = new \DateTime();
        $Post2->Tags = new \ArrayObject([]);
        $this->AddTags($Post2);
        $Blog->Posts[] = $Post2;
    }

    public function AddTags(Entities\Post $Post) {
        $Names = ['Tagged', 'Tummy', 'Tailgater', 'Food Fight', 'Andy'];
        
        for ($Count = 500; $Count > 0; $Count--) {
            $Tag = new Entities\Tag();
            $Tag->Name = $Names[rand(0, count($Names) - 1)];
            $Tag->Description = 'This is a description - ' . $Count;
            $Tag->Number = $Count * $Count;
            $Post->Tags[] = $Tag;
        }
    }

}

return new One();
?>