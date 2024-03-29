<?php
namespace AppBundle\Controller;
// We include the entity that we create in our Controller file
use AppBundle\Entity\Todo;
//use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Now we need some classes in our Controller because we need that in our form (for the inputs that we will create)
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class TodoController extends Controller
{
   /**
    * @Route("/", name="todo_list")
    */
   public function listAction(){

// Here we will use getDoctrine to use doctrine and we will select the entity that we want to work with and we used findAll() to bring all the information from it and we will save it inside a variable named todos and the type of the result will be an array 
       $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
       // replace this example code with whatever you need
       return $this->render('todo/index.html.twig', array('todos'=>$todos));
   }
   
    /**
    * @Route("/todo/create", name="todo_create")
    */
   public function createAction(Request $request){

// Here we create an object from the class that we made 
       $todo = new Todo;

/* Here we will build a form using createFormBuilder and inside this function we will put our object and then we write add then we select the input type then an array to add an attribute that we want in our input field */
       $form = $this->createFormBuilder($todo)->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
       ->add('category', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
       ->add('description', TextareaType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
       ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High'),'attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
   ->add('due_date', DateTimeType::class, array('attr' => array('style'=>'margin-bottom:15px')))
   ->add('save', SubmitType::class, array('label'=> 'Create Todo', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))
       ->getForm();
       $form->handleRequest($request);


/* Here we have an if statement, if we click submit and if  the form is valid we will take the values from the form and we will save them in the new variables */
       if($form->isSubmitted() && $form->isValid()){
           //fetching data
           $name = $form['name']->getData();
           $category = $form['category']->getData();
           $description = $form['description']->getData();
           $priority = $form['priority']->getData();
           $due_date = $form['due_date']->getData();

// Here we will get the current date
           $now = new\DateTime('now');


/* these functions we bring from our entities, every column have a set function and we put the value that we get from the form */
           $todo->setName($name);
           $todo->setCategory($category);
           $todo->setDescription($description);
           $todo->setPriority($priority);
           $todo->setDueDate($due_date);
           $todo->setCreateDate($now);
           $em = $this->getDoctrine()->getManager();
           $em->persist($todo);
           $em->flush();
           $this->addFlash(
                   'notice',
                   'Todo Added'
                   );
           return $this->redirectToRoute('todo_list');
       }
/* now to make the form we will add this line form->createView() and now you can see the form in create.html.twig file  */
       return $this->render('todo/create.html.twig', array('form' => $form->createView()));
   }
    /**
    * @Route("/todo/edit/{id}", name="todo_edit")
    */
   public function editAction( $id, Request $request){

    /* Here we have a variable todo and it will save the result of this search and it will be one result because we search based on a specific id */
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        $now = new\DateTime('now');
    
    /* Now we will use set functions and inside this set functions we will bring the value that is already exist using get function for example we have setName() and inside it we will bring the current value and use it again */
                            $todo->setName($todo->getName());
               $todo->setCategory($todo->getCategory());
               $todo->setDescription($todo->getDescription());
               $todo->setPriority($todo->getPriority());
               $todo->setDueDate($todo->getDueDate());
               $todo->setCreateDate($now);
    
    /* Now when you type createFormBuilder and you will put the variable todo the form will be filled of the data that you already set it */
           $form = $this->createFormBuilder($todo)->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
           ->add('category', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
           ->add('description', TextareaType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
           ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High'),'attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('due_date', DateTimeType::class, array('attr' => array('style'=>'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label'=> 'Update Todo', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-botton:15px')))
           ->getForm();
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()){
               //fetching data
               $name = $form['name']->getData();
               $category = $form['category']->getData();
               $description = $form['description']->getData();
               $priority = $form['priority']->getData();
               $due_date = $form['due_date']->getData();
               $now = new\DateTime('now');
               $em = $this->getDoctrine()->getManager();
               $todo = $em->getRepository('AppBundle:Todo')->find($id);
               $todo->setName($name);
               $todo->setCategory($category);
               $todo->setDescription($description);
               $todo->setPriority($priority);
               $todo->setDueDate($due_date);
               $todo->setCreateDate($now);
            
               $em->flush();
               $this->addFlash(
                       'notice',
                       'Todo Updated'
                       );
               return $this->redirectToRoute('todo_list');
           }
           return $this->render('todo/edit.html.twig', array('todo' => $todo, 'form' => $form->createView()));
       }
    /**
    * @Route("/todo/details/{id}", name="todo_details")
    */
    public function detailsAction($id){
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
    return $this->render('todo/details.html.twig', array('todo' => $todo));
    }

    
    /**
    * @Route("/todo/delete/{id}", name="todo_delete")
    */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
    $todo = $em->getRepository('AppBundle:Todo')->find($id);
    $em->remove($todo);
    $em->flush();
    $this->addFlash(
        'notice',
        'Todo Removed'
        );
    return $this->redirectToRoute('todo_list');
    }
}