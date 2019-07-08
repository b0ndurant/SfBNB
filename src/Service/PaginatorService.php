<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginatorService
{
    private $entityClass;
    private $limit;
    private $currentPage;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    /**
     * Constructor of paging service called by symfony
     * 
     * Don't forget to configure your service.yaml so Symfony knows the value $templatePath
     *
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    public function display()
    {
        $this->twig->display('admin/partials/_paginator.html.twig', [
            'page' => $this->currentPage,
            'nbPages' => $this->getPages(),
            'route' => $this->route,
        ]
        );
    }

    /**
     * Get the number pages existing on entity specific
     *
     * @return int
     */
    public function getPages()
    {
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    /**
     * Get data paginate for an entity specific
     *
     * @throws Exception if property $entityClass or $limit or $currentPage is not defined
     * @return @array
     */
    public function getData()
    {
        if (empty($this->limit)) {
            throw new \Exception("Vous n'avez pas spécifié la méthode setLimit() de votre objet paginatorService !");
        }

        if (empty($this->currentPage)) {
            throw new \Exception("Vous n'avez pas spécifié la méthode setCurrentPage() de votre objet paginatorService !");
        }

        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié la méthode setEntityClass() de votre objet paginatorService !");
        }

        $start = $this->currentPage * $this->limit - $this->limit;

        return $this->manager
            ->getRepository($this->entityClass)
            ->findBy([], [], $this->limit, $start);
    }

    /**
     * Get the value of entityClass
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the value of route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the value of route
     *
     * @return  self
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get the value of templatePath
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set the value of templatePath
     *
     * @return  self
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
}
