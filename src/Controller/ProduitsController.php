<?php 

namespace App\Controller;

use App\Entity\Produits;
use App\Form\FormProdType;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProduitsController extends AbstractController{
    #[Route('/produit', name: 'Produits', methods: ['GET', 'POST'])]
    public function Produits(ProduitsRepository $produitsRepository){
        $prod = new Produits();

        $prod
            ->setName('vibro')
            ->setDescription('wesh ça vibre')
            ->setPrice(30)
            ->setQuantity(10)
            ->setActive(true);

        $produitsRepository->add($prod);
    }

    // crée une nouvelle route
    #[Route('/addProduct', name:'addProduct', methods: ['GET', 'POST'])]
    public function addProduits(ProduitsRepository $produitsRepository, Request $request)
    {
        // crée un nouveau produit
        $prod = new Produits();
        // creer le patron du form et lui dire que ça va etre une nouvelle instance de l'entité
        $formProd = $this->createForm(FormProdType::class, $prod);
        // preparation pour l'envoie et recup le get ou le post
        $formProd->handleRequest($request);

        // si le form est valide et envoyé alors faire le script
        if ($formProd->isSubmitted() && $formProd->isValid()){
            // met l'active du l'utilisateur en true
            $prod->setActive(true);
            // ajoute en bbd les infos de la variable $prod
            $produitsRepository->add($prod);

            // faire une redirection vers la page "showProduct" grace à son name
            return $this->redirectToRoute('showProduct');
        }

        // envoyer toute les infos dans la page .twig
        return $this->render('produits/addProduits.html.twig', [
            // décalre la variable pour le .twig
            'prods' => $prod,
            // creer un visuel de la variable formProd
            'formProd' => $formProd->createView()
        ]);
    }

    #[Route('/showProduct', name:'showProduct', methods: ['GET', 'POST'])]
    public function showProduits(ProduitsRepository $produitsRepository)
    {
        // $prods = $produitsRepository->findAll();
        $prodActive = $produitsRepository->findBy([
                'active'=>true
            ],
            [
                'id'=>'DESC'
            ]);
        $prodInnactive = $produitsRepository->findBy([
            'active'=>false
        ]);

        // retourne les liens sélectionné vers le .twig
        return $this->render('produits/prod.html.twig', [
            // 'prods' => $prods,
            'prodActive'=> $prodActive,
            'prodInnactive'=> $prodInnactive
        ]);
    }

    #[Route('/changeActiveProd/{id}', name:'changeActiveProd', methods: ['GET', 'POST'])]
    public function changeActiveProd(ProduitsRepository $produitsRepository, $id){
        // choisi comme id les élément de produit repository
        $prod = $produitsRepository->findOneBy([
                'id'=>$id
            ]);

        if ($prod->getActive() == true){
            // met l'active en false
            $prod->setActive(false);
        }else{
            $prod->setActive(true);
        }


        // uptade la bdd
        $produitsRepository->add($prod);

        // redirection vers le name de la public function
        return $this->redirectToRoute('showProduct');
    }

    //                  recup l'id en get
    //                             |
    //                            \/
    #[Route('/deleteInactiveProd/{id}', name: 'deleteInnnactiveProd', methods: ['GET', 'POST'])]
    public function deleteInnnactiveProd(ProduitsRepository $produitsRepository, $id)
    {
        $prod = $produitsRepository->findOneBy([
            'id'=>$id
        ]);

        if ($prod->getActive() == false){
            $produitsRepository->remove($prod);
        }
        return $this->redirectToRoute('showProduct');
    }

    #[Route('/showOneProduct/{id}', name: 'showOneProduct', methods: ['GET', 'POST'])]
    public function showOneProduct(ProduitsRepository $produitsRepository, $id){
        $prod = $produitsRepository->findOneBy([
            'id'=>$id
        ]);
        return $this->render('produits/showOneProduct.html.twig', [
            'prod'=>$prod
        ]);
    }

    #[Route('/modifProduct/{id}', name: 'modifProduct', methods: ['GET', 'POST'])]
    public function modifProduct(ProduitsRepository $produitsRepository,Request $request, $id){
        $prod = $produitsRepository->findOneBy([
            'id'=>$id
        ]);
        $formModifProd = $this->createForm(FormProdType::class, $prod);
        $formModifProd->handleRequest($request);

        if ($formModifProd->isSubmitted() && $formModifProd->isValid()){
            // met l'active du l'utilisateur en true
            $prod->setActive(true);
            // ajoute en bbd les infos de la variable $prod
            $produitsRepository->add($prod);

            // faire une redirection vers la page "showProduct" grace à son name
            return $this->redirectToRoute('showProduct');
        }

        return $this->render('produits/modifProduct.html.twig', [
            'prod'=>$prod,
            'formModifProd'=>$formModifProd->createView()
        ]);
    }
}