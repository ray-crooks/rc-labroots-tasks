<?php
namespace AppBundle\Entity;

class Task
{
	private $id;
    private $task_name;
    private $priority;
    private $created_at;
	private $updated_at;
	
	public function __get($property) {
		return $this->$property;
	}

	public function __set($property, $value) {
		$this->$property = $value;
	}  
}