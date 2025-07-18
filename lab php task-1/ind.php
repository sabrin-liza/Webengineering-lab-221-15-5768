<?php
class Book{
    public $title;
    public $author;
    public $description;
    public function __construct($title, $author, $description) {
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
    }
    public function isavaiable($title) {
        switch ($this->title == $title) {
            case true:
                return "'$title' is available.";
            default:
                return "'$title' is not available.";
        }
    }
    
    public function description() {
        return "Title: $this->title, Author: $this->author, Description: $this->description";
    }

    public static function bookObject($books) {
        $bookList = [];
        foreach ($books as $book) {
            $bookList[] = $book->title;
        }
        echo "Total Book List: " . implode(", ", $bookList) . "<br>";
    }
}

$b1 = new Book("The poem", "Fitzgerald", "A novel ");
$b2 = new Book("Story", "Orwell", "A dystopian novel ");
echo $b1->isavaiable("The Great") . "<br>";
echo $b2->isavaiable("The poem") . "<br>";


Book::bookObject([$b1, $b2]);


?>