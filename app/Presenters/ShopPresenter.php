<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Image;

final class ShopPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;


	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}


        	public function renderDefault(): void
        {
                $this->template->produkty= $this->database
                        ->table('produkt')
                        ->order('id DESC')
                        ->limit(5);
        }
        
        public function renderProdukt(int $pid): void
	{
		
                $this->template->produkt = $this->database->table('produkt')->get($pid);
		

	}
        
         public function renderEdit(int $pid): void
	{       
                $post = $this->database
			->table('produkt')
			->get($pid);
		$this->getComponent('productForm')
			->setDefaults($post->toArray());
                $this->template->produkt = $this->database->table('produkt')->get($pid);
		

	}
        
        protected function createComponentProductForm(): Form
	{
		$form = new Form;
                $form->addText('popisek', 'Popisek')
                       ->setRequired(); 
		$form->addText('cena', 'Cena:') 
			->setRequired();
                $form->addRadioList('dostupnost', 'Dostupnost', ["skladem" => "Skladem", "vyprodano" => "Vyprodáno"]) 
                      ->setRequired();
                $form->addUpload('upload', 'Fotografie:')
                        ->addRule($form::IMAGE, 'Avatar musí být JPEG, PNG, GIF or WebP.')
                        ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 1 MB.', 1024 * 1024);
		$form->addSubmit('send', 'Ulož produkt');
		$form->onSuccess[] = [$this, 'productFormSucceeded'];

		return $form;
	}
	
        public function productFormSucceeded($form, $values): void
	{
		$pid = $this->getParameter('pid');
              
             
                    $soubor = $values->upload;
                    if($values->upload->hasFile())
                        $soubor->move("upload/" . $values->upload->name);
                    else
                        $values->upload=null;
                
                if ($pid) {
                    $produkt = $this->database
                            ->table('produkt')
                            ->get($pid);
                    $produkt->update($values);

                    } else {
                        $produkt = $this->database
                            ->table('produkt')
                            ->insert($values);
                    }
                
           
            
            
		$this->flashMessage('Uloženo', 'success');
		//$this->redirect('Shop:default', $produkt->id);
	
            
             }
            
        
        

}
