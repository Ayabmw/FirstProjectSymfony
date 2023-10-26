<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function searchBookByRef($ref)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.IdAuthor', 'a')
            ->orderBy('a.username', 'ASC') 
            ->getQuery()
            ->getResult();
    }
    public function findPublishedBooksBeforeYearWithMoreThan10Books()
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.IdAuthor', 'a')
            ->andWhere('b.published = true')
            ->andWhere('b.publicationDate < :year')
            ->andWhere('a.nb_books > 10')
            ->setParameter('year', new \DateTime('2023-01-01'));

        return $queryBuilder->getQuery()->getResult();
    }
    public function updateScienceFictionBooksToRomance()
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->andWhere('b.category = :Science-Fiction')
            ->setParameter('Science-Fiction', 'Science-Fiction');

        $scienceFictionBooks = $queryBuilder->getQuery()->getResult();

        foreach ($scienceFictionBooks as $book) {
            $book->setCategory('Romantique');
            $this->_em->persist($book);
        }

        $this->_em->flush();
    }
    public function countRomanceBooks(): int
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('COUNT(b)')
            ->andWhere('b.category = :romantique')
            ->setParameter('romantique', 'Romantique');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
    public function findByNumberOfBooks(?int $min, ?int $max): array
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($min !== null) {
            $queryBuilder->andWhere('a.nb_books >= :min')
                ->setParameter('min', $min);
        }

        if ($max !== null) {
            $queryBuilder->andWhere('a.nb_books <= :max')
                ->setParameter('max', $max);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
