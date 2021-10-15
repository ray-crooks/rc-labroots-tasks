<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Task;

class DefaultController extends Controller
{    
    public function indexAction(Request $request)
    {
		$tasks = [];
		
		$task_query = $this->getDoctrine()
			->getRepository(Task::class)
			->findAll();

		foreach ($task_query as $tq)
		{
			$tasks[] = [
				"id" => $tq->id,
				"task_name" => $tq->task_name,
				"priority" => $tq->priority,
				"created_at" => $tq->created_at->format("m/d/Y H:i:s"),
				"updated_at" => $tq->updated_at->format("m/d/Y H:i:s"),
			];
		}
	
		return $this->render('tasks/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			'tasks' => $tasks,
        ]);
    }
	
	public function createAction(Request $request)
	{
		$status = false;
		$reqdata = $request->request->all();
		
		if (isset($reqdata['id']) && $reqdata['id'] == "0" && isset($reqdata['task_name']) && isset($reqdata['priority']) && trim($reqdata['task_name']) != "" && trim($reqdata['priority']) != "")
		{
			$entityManager = $this->getDoctrine()->getManager();

			$task = new Task();
			$task->task_name 	= $reqdata['task_name'];
			$task->priority 	= $reqdata['priority'];
			$task->created_at 	= new \DateTime("now");
			$task->updated_at 	= new \DateTime("now");

			$entityManager->persist($task);
			$entityManager->flush();

			$status = true;
		}
		
		$response = [
			"status" => $status
		];
		
		return new Response(json_encode($response));
	}

	public function updateAction(Request $request)
	{
		$status = false;
		$reqdata = $request->request->all();
		
		if (!empty($reqdata['id']) && isset($reqdata['task_name']) && isset($reqdata['priority']) && trim($reqdata['task_name']) != "" && trim($reqdata['priority']) != "")
		{
			$entityManager = $this->getDoctrine()->getManager();
			$task = $entityManager->getRepository(Task::class)->find($reqdata['id']);
			
			if (!$task)
			{
				// Do nothing, status will return false
			}
			else
			{
				$task->task_name 	= $reqdata['task_name'];
				$task->priority 	= $reqdata['priority'];
				$task->updated_at 	= new \DateTime("now");
				$entityManager->flush();
				
				$status = true;
			}
		}
		
		$response = [
			"status" => $status
		];
		
		return new Response(json_encode($response));
	}
	
	public function detailAction(int $taskId)
	{
		$task_query = $this->getDoctrine()
			->getRepository(Task::class)
			->find($taskId);

		if (!$task_query) {
			throw $this->createNotFoundException(
				'No task found for id '.$taskId
			);
		}
		
		$task = [
			"id" => $task_query->id,
			"task_name" => $task_query->task_name,
			"priority" => $task_query->priority,
			"created_at" => $task_query->created_at->format("m/d/Y H:i:s"),
			"updated_at" => $task_query->updated_at->format("m/d/Y H:i:s"),
		];

		return new Response(json_encode($task));
	}
	
	public function deleteAction(Request $request)
	{
		$status = false;
		$reqdata = $request->request->all();
		
		if (!empty($reqdata['id']))
		{	
			$entityManager = $this->getDoctrine()->getManager();
			$task = $entityManager->getRepository(Task::class)->find($reqdata['id']);

			if (!$task) {
				// Do nothing, status will return false
			}
			else
			{
				$entityManager->remove($task);
				$entityManager->flush();
				
				$status = true;
			}
		}
		
		$response = [
			"status" => $status
		];
		
		return new Response(json_encode($response));	
	}
}
