<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agent;
use AppBundle\Entity\Beneficiaire;
use AppBundle\Entity\Solde;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\HttpFoundation\Response;

# Controlleur de l'agent
class AgentController extends Controller
{
	/**
	*@Route("/agent", name="view_agent_route")
	*/
	public function viewAgentAction(Request $request){
		$agents = $this->getDoctrine()->getRepository('AppBundle:Agent')->findAll();

		/**
		* @var $paginator \knp\Component\Pager\Paginator
		*/
		$paginator = $this->get('knp_paginator');
		$result = $paginator -> paginate(
			$agents,
			$request->query->getInt('page', 1),
			$request->query->getInt('limit', 5)
		);

		return $this->render("agent/index.html.twig", ['agents' => $result]);
	}

	/**
	*@Route("/agent/create", name="create_agent_route")
	*/
	public function createAgentAction(Request $request){
		$agent = new Agent;
		$form = $this->createFormBuilder($agent)
			->add('matricule', TextType::Class, array('label' => 'Numéro Matricule','attr' => array('class' => 'form-control')))
			->add('nom', TextType::Class, array('label' => 'Nom et Prenoms','attr' => array('class' => 'form-control')))
			->add('budget', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('grade', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('indice', TextType::Class, array('attr' => array('class' => 'form-control'))) 

			->add('situation', ChoiceType::Class, array(
				'choices' => array(
					'Fonctionnaires de l\'Etat' => 'En application des dispositions de l’article 37 de la loi n°2003/011 du 03 Septembre 2003, relative au statut général des fonctionnaires de l’Etat.',
					'Non Encadrée de l\'Etat' => 'En application des dispositions de l’article 35 de la loi n°94-025 du 17 Novembre 1994, relative au statut général des Agents non encadrés de l’Etat.',
					'Retraité' => 'En Application des dispositions du Décret n°62-144 du 21 Mars 1962, portant organisation de règlement de la Caisse de Retraites Civiles et Militaires et le décret n°89-094 du 12 Avril 1989 modifiant certaines dispositions du décret n°62-144 susvisé notamment en son article 13.',
				),
				'attr' => array(
					'class' => 'form-control'
				),
			))

			->add('dateDeces', TextType::Class, array('label' => 'Date décès','attr' => array('class' => 'form-control', 'placeholder' => 'jour-mois-année'))) 
			->add('suivant', TextType::Class, array('label' => 'Suivant acte numéro','attr' => array('class' => 'form-control'))) 
			->add('compterDu', TextType::Class, array('attr' => array('class' => 'form-control', 'placeholder' => 'jour-mois-année'))) 
			->add('jusqu', TextType::Class, array('attr' => array('class' => 'form-control', 'placeholder' => 'jour-mois-année'))) 
			->add('lieuservice', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('chapitre', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('zone', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('nbenfant', TextType::Class, array('attr' => array('class' => 'form-control')))

			->add('beneficiaire', EntityType::Class, array(
				'class' => 'AppBundle\Entity\Beneficiaire',
				'choice_label' => 'id',
				'attr' => array('class' => 'form-control')))

			->add('solde', EntityType::Class, array(
				'class' => 'AppBundle\Entity\Solde',
				'choice_label' => 'id',
				'attr' => array('class' => 'form-control')))

			->add('save', SubmitType::Class, array('label' => ' Enregistrer', 'attr' => array('class' => 'fa fa-floppy-o btn btn-primary', 'style' => 'margin-top: 20px; margin-bottom: 20px')))
			->getForm();
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()){
				$matricule = $form['matricule']->getData();
				$nom = $form['nom']->getData();
				$budget = $form['budget']->getData();
				$grade = $form['grade']->getData();
				$indice = $form['indice']->getData();
				$situation = $form['situation']->getData();
				$datedec = $form['dateDeces']->getData();
				$suivant = $form['suivant']->getData();
				$compter = $form['compterDu']->getData();
				$jusqu = $form['jusqu']->getData();
				$lieuserv = $form['lieuservice']->getData();
				$chapitre = $form['chapitre']->getData();
				$zone = $form['zone']->getData();
				$nbenfant = $form['nbenfant']->getData();
				$benefic = $form['beneficiaire']->getData();
				$sold = $form['solde']->getData();

				$agent->setMatricule($matricule);
				$agent->setNom($nom);
				$agent->setBudget($budget);
				$agent->setGrade($grade);
				$agent->setIndice($indice);
				$agent->setSituation($situation);
				$agent->setDateDeces($datedec);
				$agent->setSuivant($suivant);
				$agent->setCompterDu($compter);
				$agent->setJusqu($jusqu);
				$agent->setLieuservice($lieuserv);
				$agent->setChapitre($chapitre);
				$agent->setZone($zone);
				$agent->setNbenfant($nbenfant);
				$agent->setBeneficiaire($benefic);
				$agent->setSolde($sold);

				$em = $this->getDoctrine()->getManager();
				$em->persist($agent);
				$em->flush();
				$this->addFlash('message', 'Agent Ajouter avec succes!');
				return $this->redirectToRoute('view_agent_route');
			} 
		return $this->render("agent/create.html.twig", ['form' => $form->createView()]);
	}

	/**
	*@Route("/agent/update/{id}", name="update_agent_route")
	*/
	public function updateAgentAction(Request $request, $id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);

		$agent->setMatricule($agent->getMatricule());
		$agent->setNom($agent->getNom());
		$agent->setBudget($agent->getBudget());
		$agent->setGrade($agent->getGrade());
		$agent->setIndice($agent->getIndice());
		$agent->setSituation($agent->getSituation());
		$agent->setDateDeces($agent->getDateDeces());
		$agent->setSuivant($agent->getSuivant());
		$agent->setCompterDu($agent->getCompterDu());
		$agent->setJusqu($agent->getJusqu());
		$agent->setLieuservice($agent->getLieuservice());
		$agent->setChapitre($agent->getChapitre());
		$agent->setZone($agent->getZone());
		$agent->setNbenfant($agent->getNbenfant());
		$agent->setBeneficiaire($agent->getBeneficiaire());
		$agent->setSolde($agent->getSolde());

		$form = $this->createFormBuilder($agent)
			->add('matricule', TextType::Class, array('label' => 'Numéro Matricule','attr' => array('class' => 'form-control')))
			->add('nom', TextType::Class, array('label' => 'Nom et Prenoms','attr' => array('class' => 'form-control')))
			->add('budget', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('grade', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('indice', TextType::Class, array('attr' => array('class' => 'form-control'))) 

			->add('situation', ChoiceType::Class, array(
				'choices' => array(
					'Fonctionnaires de l\'Etat' => 'En application des dispositions de l’article 37 de la loi n°2003/011 du 03 Septembre 2003, relative au statut général des fonctionnaires de l’Etat.',
					'Non Encadrée de l\'Etat' => 'En application des dispositions de l’article 35 de la loi n°94-025 du 17 Novembre 1994, relative au statut général des Agents non encadrés de l’Etat.',
					'Retraité' => 'En Appliation des dispositions du Décret n°62-144 du 21 Mars 1962, portant organisation de règlement de la Caisse de Retraites Civiles et Militaires et le décret n°89-094 du 12 Avril 1989 modifiant certaines dispositions du décret n°62-144 susvisé notamment en son article 13.',
				),
				'attr' => array(
					'class' => 'form-control'
				),
			))

			//->add('situation', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('dateDeces', TextType::Class, array('label' => 'Date décès','attr' => array('class' => 'form-control','placeholder' => 'jour-mois-année'))) 
			->add('suivant', TextType::Class, array('label' => 'Suivant acte numéro','attr' => array('class' => 'form-control'))) 
			->add('compterDu', TextType::Class, array('attr' => array('class' => 'form-control', 'placeholder' => 'jour-mois-année'))) 
			->add('jusqu', TextType::Class, array('attr' => array('class' => 'form-control', 'placeholder' => 'jour-mois-année'))) 
			->add('lieuservice', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('chapitre', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('zone', TextType::Class, array('attr' => array('class' => 'form-control'))) 
			->add('nbenfant', TextType::Class, array('attr' => array('class' => 'form-control')))
			->add('beneficiaire', EntityType::Class, array(
				'class' => 'AppBundle\Entity\Beneficiaire',
				'choice_label' => 'id',
				'attr' => array('class' => 'form-control')))

			->add('solde', EntityType::Class, array(
				'class' => 'AppBundle\Entity\Solde',
				'choice_label' => 'id',
				'attr' => array('class' => 'form-control')))

			->add('save', SubmitType::Class, array('label' => ' Enregistrer', 'attr' => array('class' => 'fa fa-floppy-o btn btn-primary', 'style' => 'margin-top: 20px; margin-bottom: 20px')))
			->getForm();
			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()){
				$matricule = $form['matricule']->getData();
				$nom = $form['nom']->getData();
				$budget = $form['budget']->getData();
				$grade = $form['grade']->getData();
				$indice = $form['indice']->getData();
				$situation = $form['situation']->getData();
				$datedec = $form['dateDeces']->getData();
				$suivant = $form['suivant']->getData();
				$compter = $form['compterDu']->getData();
				$jusqu = $form['jusqu']->getData();
				$lieuserv = $form['lieuservice']->getData();
				$chapitre = $form['chapitre']->getData();
				$zone = $form['zone']->getData();
				$nbenfant = $form['nbenfant']->getData();
				$benefic = $form['beneficiaire']->getData();
				$sold = $form['solde']->getData();

				$em = $this->getDoctrine()->getManager();
				$agent = $em->getRepository('AppBundle:Agent')->find($id);

				$agent->setMatricule($matricule);
				$agent->setNom($nom);
				$agent->setBudget($budget);
				$agent->setGrade($grade);
				$agent->setIndice($indice);
				$agent->setSituation($situation);
				$agent->setDateDeces($datedec);
				$agent->setSuivant($suivant);
				$agent->setCompterDu($compter);
				$agent->setJusqu($jusqu);
				$agent->setLieuservice($lieuserv);
				$agent->setChapitre($chapitre);
				$agent->setZone($zone);
				$agent->setNbenfant($nbenfant);
				$agent->setBeneficiaire($benefic);
				$agent->setSolde($sold);


				$em->flush();
				$this->addFlash('message', 'Modification Agent avec succes!');
				return $this->redirectToRoute('view_agent_route');
			}
		return $this->render("agent/update.html.twig", ['form' => $form->createView()]);
	}

	/**
	*@Route("/agent/show/{id}", name="show_agent_route")
	*/
	public function showAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);

		return $this->render("agent/view.html.twig", ['agent' => $agent]);
	}

	
	/**
	*@Route("/agent/delete/{id}", name="delete_agent_route")
	*/
	public function deleteAgentAction($id){
		$em = $this->getDoctrine()->getManager();
		$agent = $em->getRepository('AppBundle:Agent')->find($id);
		$em->remove($agent);
		$em->flush();
		$this->addFlash('message', 'Agent Bien Supprimer!');
		return $this->redirectToRoute('view_agent_route');
	}

	/**
	*@Route("/agent/exportationpdf/{id}", name="exportpdf_agent_route")
	*/
	
	public function exportPDFAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");

		$html = $this->renderView("exportation/pdfagent.html.twig", ['agent' => $agent]);

		$filename = "custom_pdf_from_twig";

		//return $this->render("pages/view.html.twig", ['agent' => $agent]);

		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="secours.pdf"'
			)
		);
	}
	
	/**
	*@Route("/agent/exportationpdfdecompte/{id}", name="exportationpdfdecompte_agent_route")
	*/
	
	public function exportPDFdecompteAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$html = $this->renderView("exportation/pdfdecompte.html.twig", ['agent' => $agent]);

		//return $this->render("pages/view.html.twig", ['agent' => $agent]);

		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="Etat_Décompte.pdf"'

			)
		);
	}


	/**
	*@Route("/agent/exportationpdfdecision/{id}", name="exportationpdfdecision_agent_route")
	*/
	
	public function exportPDFdecisionAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$snappy -> setOption("orientation","landscape");
		$html = $this->renderView("exportation/pdfdecision.html.twig", ['agent' => $agent]);


		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="Décision.pdf"'

			)
		);
	}

	/**
	*@Route("/agent/recherche", name="recherche_agent_route")
	*/
	public function rechercheAction(Request $request){

		$motcle = $request->get('motcle');
		//$agents = $this->getDoctrine()->getRepository('AppBundle:Agent')->findBy( array('matricule' => $motcle));
		$agents = $this->getDoctrine()->getRepository('AppBundle:Agent');
		$query = $agents->createQueryBuilder('a')
			->where('a.nom like :nom')
			->setParameter('nom', '%'.$motcle.'%')
			->orderBy('a.nom', 'ASC')
			->getQuery();

		$agents = $query->getResult();

		/**
		* @var $paginator \knp\Component\Pager\Paginator
		*/
		$paginator = $this->get('knp_paginator');
		$result = $paginator -> paginate(
			$agents,
			$request->query->getInt('page', 1),
			$request->query->getInt('limit', 5)
		);

		return $this->render("agent/index.html.twig", ['agents' => $result]);
	}


	/**
	*@Route("/agent/pdffonctdecompte/{id}", name="expopdfdecision_agent_route")
	*/
	
	public function expdecisionAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$html = $this->renderView("exportation/pdffonctdecompte.html.twig", ['agent' => $agent]);


		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="Décompte.pdf"'

			)
		);
	}

	/**
	*@Route("/agent/pdffonctdecision/{id}", name="exportationdecision_agent_route")
	*/
	
	public function expoPDFdecisionAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$snappy -> setOption("orientation","landscape");
		$html = $this->renderView("exportation/pdffonctdecision.html.twig", ['agent' => $agent]);


		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="Decision.pdf"'

			)
		);
	}

	/**
	*@Route("/agent/pdfccpretrait/{id}", name="exportationretrait_agent_route")
	*/
	
	public function expoPDFccpretraitAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$html = $this->renderView("exportation/pdfccpretrait.html.twig", ['agent' => $agent]);


		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="Certificat.pdf"'

			)
		);
	}

	/**
	*@Route("/agent/pdfdecmpretrait/{id}", name="expdecompretrait_agent_route")
	*/
	
	public function expoPDFdecmpretraitAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$html = $this->renderView("exportation/pdfdecmpretrait.html.twig", ['agent' => $agent]);


		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="DécompteRetrait.pdf"'

			)
		);
	}

	/**
	*@Route("/agent/pdfdecisionretraite/{id}", name="expdecisretrait_agent_route")
	*/
	
	public function expoPDFdecretraiteAgentAction($id){
		$agent = $this->getDoctrine()->getRepository('AppBundle:Agent')->find($id);
		
		$snappy = $this->get("knp_snappy.pdf");
		$snappy -> setOption("orientation","landscape");
		$html = $this->renderView("exportation/pdfdecisionretraite.html.twig", ['agent' => $agent]);


		return new Response(
			$snappy->getOutputFromHtml($html),
			// Status Code OK
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline ; filename="DécisionRetrait.pdf"'

			)
		);
	}

}

