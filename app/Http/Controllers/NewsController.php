<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use URL;

use App\Page;
use App\Article;
use App\Comment;

class NewsController extends Controller
{
    public function news($lang)
    {
        $page = Page::where('slug', 'news')->where('lang', $lang)->first();
        $articles = Article::orderBy('created_at')->where('lang', $lang)->paginate(15);

        return view('news', ['page' => $page, 'articles' => $articles]);
    }

    public function newsSingle($lang, $slug)
    {
        $page = Page::where('slug', 'news')->where('lang', $lang)->first();
        $article = Article::where('slug', $slug)->where('lang', $lang)->first();

        return view('news-single', ['page' => $page, 'article' => $article]);
    }

    public function articlesCategory($page)
    {
        $articlesCategory = Page::where('slug', $page)->first();
        $articlesCategories = Page::where('slug', 'articles')->get()->toTree();
        $articles = Article::where('page_id', $articlesCategory->id)->paginate(10);

        return view('pages.articles-category', compact('articlesCategory', 'articles', 'articlesCategories'));
    }

    public function saveComment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required|min:5|max:500',
        ]);

        $url = explode('articles/', URL::previous());
        $newsSingle = Article::where('slug', $url[1])->first();

        if ($request->id == $articleSingle->id) {
            $comment = new Comment;
            $comment->parent_id = $request->id;
            $comment->parent_type = 'App\Article';
            $comment->name = \Auth::user()->name;
            $comment->email = \Auth::user()->email;
            $comment->comment = $request->comment;
            // $comment->stars = (int) $request->stars;
            $comment->save();
        }

        if ($comment) {
            return redirect()->back()->with('status', 'Отзыв добавлен!');
        }
        else {
            return redirect()->back()->with('status', 'Ошибка!');
        }
    }
}
