<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        // Get All Book
        // $books=Book::get();

        //This is normal function style
        // $books = Book::when($title, function ($query,$title) {
        //     return $query->title($title);
        // })->get();

        //This is Arrow Function style
        $books = Book::when(
            $title,
            fn ($query, $title) => $query->title($title)
        );

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()
        };
        $cacheKey = 'books:' . $filter . ':' . $title;
        $books = cache()->remember($cacheKey, 3600, fn () => $books->get());  //<------ The same Result
        // This is another style  <-----The same Result
        // $books = cache()->remember($cacheKey, 3600, function () use ($books) {
        //     return $books->get();
        // });
        // return view('books.index',compact('books'));
        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //The simple style
        // return view('books.show',['book'=>$book]);


        //  ***************
        //Another Style  Sort book->reviews relationship to latest() order ! You can alsoe filter relationship

        //Change for Invalidation cache
        // return view('books.show', [
        //     'book' => $book->load([
        //         'reviews' => fn ($query) => $query->latest()
        //     ])
        // ]);

        $cacheKey = 'book:' . $book->id;
        $book = cache()->remember($cacheKey, 3600, $book->load([
            'reviews' => fn ($query) => $query->latest()
        ])  );


        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
