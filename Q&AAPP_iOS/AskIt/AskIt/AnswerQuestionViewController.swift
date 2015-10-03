//
//  AnswerQuestionViewController.swift
//  AskIt
//
//  Created by Komran Ghahremani on 1/3/15.
//  Copyright (c) 2015 Komreezy. All rights reserved.
//

import UIKit

class AnswerQuestionViewController: UIViewController, UITextViewDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate {

    @IBOutlet var textView: UITextView!
    @IBOutlet var descriptionLabel: UILabel!
    var picker = UIImagePickerController()
    
    
    //default view controller methods
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
        
        //Placeholder initial
        self.textView.text = "Answer the Question.."
        self.textView.textColor = UIColor.lightGrayColor()
        
        //get the text to start at the top -- was starting in middle for some reason
        self.textView.setContentOffset(CGPointMake(0, -self.textView.contentInset.top), animated: true)
        
        //set the description for the user to see the question
        self.descriptionLabel.text = manager.currentTitle
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    
    
    
    //coloring the nav bar and adding logo in the center
    override func viewDidAppear(animated: Bool) {
        // 1
        var nav = self.navigationController?.navigationBar
        
        nav?.tintColor = UIColor.whiteColor()
        // 3
        let imageView = UIImageView(frame: CGRect(x: 0, y: 0, width: 40, height: 40))
        imageView.contentMode = .ScaleAspectFit
        // 4
        let image = UIImage(named: "NavBarLogo")
        imageView.image = image
        // 5
        navigationItem.titleView = imageView
    }
    
    
    
    
    //Placeholder text
    func textViewDidBeginEditing(textView: UITextView) {
        if textView.text == "Answer the Question.."{
            textView.text = ""
            textView.textColor = UIColor.blackColor()
        }
        textView.becomeFirstResponder()
    }
    
    func textViewDidEndEditing(textView: UITextView) {
        if textView.text == ""{
            textView.text = "Answer the Question.."
            textView.textColor = UIColor.lightGrayColor()
        }
        textView.resignFirstResponder()
    }
    
    
    
    
    
    //If user hits the return key or taps outside of text view --> get rid of the keyboard
    func textView(textView: UITextView!, shouldChangeTextInRange: NSRange, replacementText: NSString!) {
        if(replacementText == "\n") {
            textView.resignFirstResponder()
        }
    }
    
    override func touchesBegan(touches: NSSet, withEvent event: UIEvent) {
        self.textView.endEditing(true)
    }

    
    
    
    //open up camera
    @IBAction func takePhoto(sender: UIButton) {
        
        picker.delegate = self
        picker.allowsEditing = true
        picker.sourceType = UIImagePickerControllerSourceType.Camera
        
        self.presentViewController(picker, animated: true, completion: nil)
    }

    
}
