<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/AddBook', name: 'app_AddBook')]
    public function AddBook(Request $request, ManagerRegistry $doctrine): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            // Rediriger l'utilisateur vers la page de liste des livres après l'ajout
            return $this->redirectToRoute('app_all_book');
        }

        return $this->renderForm('book/addLivre.html.twig', ['myform' => $form]);
    }
    #[Route('/book/all', name: 'app_all_book')]
    public function getAll(ManagerRegistry $doctrine): Response
    {
        $repo=$doctrine->getRepository(Book::class);
        $books=$repo->findAll();
        return $this->render('book/listbook.html.twig',
        ['maliste'=>$books]);
        //return $this->render('author/showauthor.html.twig', ['ida'=>$id]);
    }
    #[Route('/book/published', name: 'app_published_book')]
public function getPublished(ManagerRegistry $doctrine): Response
{
    $repo = $doctrine->getRepository(Book::class);
    $books = $repo->findBy(['published' => true]);

    $nb1 = count($books);
    $nb2 = count (['published' => false]);
    return $this->render('book/listbook.html.twig', [
        'maliste' => $books,
        'nbpub' => $nb1,
        'nbna' => $nb2,
    ]);
}

    


    #[Route("/book/{ref}/edit", name:"edit_book")]
    public function editBook(Request $request, Book $book): Response
    {
        $form = $this->createFormBuilder($book)
            ->add('Title')
            ->add('category')
            ->add('published') // Si "published" est de type booléen, utilisez CheckboxType::class
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Redirigez l'utilisateur après la modification du livre
            return $this->redirectToRoute('listbook.html.twig');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
#[Route("/books/{ref}/delete", name:"delete_book")]
public function deleteBook($ref): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $book = $entityManager->getRepository(Book::class)->find($ref);

    if (!$book) {
        throw $this->createNotFoundException('Le livre avec l\'ID '.$ref.' n\'existe pas.');
    }

    // Supprimez le livre
    $entityManager->remove($book);
    $entityManager->flush();

    // Redirigez vers la liste des livres après la suppression
    return $this->redirectToRoute('listbook.html.twig');
}

#[Route("/books/{ref}", name: "show_book")]
public function showBook(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
    #[Route('/books-by-title/{title}', name: 'app_booksByTitle')]
    public function booksByTitle( BookRepository $bookRepository, string $title)
    {
        // Rechercher les livres par titre
        $books = $bookRepository->findBy(['title' => $title]);
    
        return $this->render('book/show.html.twig', [
            'title' => $title,  // Passer le titre à la vue
            'books' => $books,
        ]);
    }
    #[Route("/searchBook", name: "app_search_books")]
    public function searchBooks(Request $request, BookRepository $bookRepository): Response
    {
        $searchTerm = $request->query->get('search');

        // Utilisez la méthode searchBookByRef du repository pour récupérer le livre par la référence
        $books = $bookRepository->searchBookByRef($searchTerm);

        return $this->render('book/listbook.html.twig', [
            'maliste' => $books,
        ]);
    }
}



