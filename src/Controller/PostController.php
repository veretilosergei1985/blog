<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="posts")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository)
    {
        return $this->render('post/list.html.twig', [
            'posts' => $postRepository->findBy([], ['createdAt' => 'DESC'])
        ]);
    }

    /**
     * @Route("/posts/{id}", name="post_view")
     * @param Post $post
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function view(Post $post, Request $request)
    {
        $comment = new Comment();
        $comment->setPost($post);
        $form = $this->createForm(CommentType::class, $comment);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('post_view', ['id' => $post->getId()]);
        }

        return $this->render('post/view.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/posts/new", name="post_create")
     * Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $post->setUser($this->getUser());

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/posts/{id}/edit", name="post_edit")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(int $id, PostRepository $postRepository, Request $request)
    {
        $post = $postRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if (!$post) {
            throw new NotFoundHttpException(sprintf("Post with id '%s' not found", $id));
        }
        $form = $this->createForm(PostType::class, $post);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }
        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/posts/{id}/delete", name="post_delete")
     * @param Post $post
     * @return RedirectResponse
     */
    public function delete(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }
}
