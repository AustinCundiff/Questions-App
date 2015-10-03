//
//  QuestionManager.swift
//  AskIt
//
//  Created by Komran Ghahremani on 12/28/14.
//  Copyright (c) 2014 Komreezy. All rights reserved.
//

import UIKit

//global variable - so that other classes can store data/call methods
var manager: QuestionManager = QuestionManager()

/*
 * Question Structure
 * 1. Title
 * 2. Category
 * 3. Number of Answers
 * 4. Preview Image
 */


//like a question object
struct question{
    var title = "Un-Named"
    var category = "No-Category"
    var numAnswers = 0
    var thumbnail: UIImage!
}

class QuestionManager: NSObject {
    //make array of question objects to hold all of the questions
    //var questions = [question]()
    var currentTitle: String = ""
    var thumbnailImage = UIImage(named: "questionmark")
    
    var questions = [question]()
    
    //function to call to add a question into the array
    //Needs all 3 parameters -- Title, Category, Number of Answers
    func addQuestion(titleParam: String, categoryParam: String, numAnswersParam: Int, thumbnailParam:UIImage){
        questions.append(question(title: titleParam, category: categoryParam, numAnswers: numAnswersParam, thumbnail: thumbnailParam))
    }
}
