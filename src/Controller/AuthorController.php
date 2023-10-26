<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', 
        [
            'controller_n' => 'AuthorControllerrrrr',
            'variable2'=>'3A37'
        ]);
    }
    #[Route('/author2', name: 'app_author')]
    public function index2(): Response
    {
        return $this->render('author/index.html.twig', 
        [
            'controller_n' => 'AuthorControllerindex2',
            'variable2'=>'3A37'
        ]);
    }

    #[Route('/showAuthor/{name}', name: 'app_show_author')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig',[
            'mavariable'=>$name
        ]);
    }

    #[Route('/listauteurs', name: 'app_list_author')]
    public function list(): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => 'images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => 'images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => 'images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
            );
            
        return $this->render('author/list.html.twig',
        ['maliste'=>$authors]);
    }

 #[Route('/detailsauthor/{id}', name: 'app_details_author')]
    public function details($id): Response
    {
        return $this->render('author/showauthor.html.twig', ['ida'=>$id]);
    }
    #[Route('/addAuthor', name: 'app_add2_author')]

    public function addAuthor(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['picture']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $fileName);
                $author->setPicture($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('app_add2_author');
        }
        return $this->render('author/addAuthor.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
    #[Route('/authors-by-email', name: 'app_authors-by-email')]
    public function authorsByEmail(AuthorRepository $authorRepository): Response
{
    $authors = $authorRepository->listAuthorByEmail();

    return $this->render('author/list.html.twig', [
        'maliste' => $authors,
    ]);
}
#[Route('/edit{id}', name: 'app_edit_author')]
public function editAuthor(Author $author, Request $request): Response
{
   
    return $this->redirectToRoute('app_show_author'); 
}
#[Route('/delete{id}', name: 'app_delete_author')]
public function deleteAuthor(Author $author, EntityManagerInterface $entityManager): Response
{
    // Remove the author from the database
    $entityManager->remove($author);
    $entityManager->flush();

    return $this->redirectToRoute('app_list_author'); 
}
#[Route('/search/authors', name: 'search_authors')]
public function searchAuthors(Request $request, AuthorRepository $authorRepository)
{
    $minBooks = $request->query->get('minBooks');
    $maxBooks = $request->query->get('maxBooks');

    $authors = $authorRepository->findByNumberOfBooks($minBooks, $maxBooks);

    return $this->render('author/list.html.twig', [
        'authors' => $authors,
    ]);
}
}