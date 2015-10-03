//
//  DescriptionViewController.swift
//  AskIt
//
//  Created by Komran Ghahremani on 12/29/14.
//  Copyright (c) 2014 Komreezy. All rights reserved.
//

import UIKit

class DescriptionViewController: UIViewController, UITextViewDelegate {
    
    @IBOutlet var textView: UITextView!
    @IBOutlet var thumbnail: UIImageView!
    var url: NSURL!
    
    
    // set up the placeholder, URL, and the default thumbnail image
    override func viewDidLoad() {
      
        //First server URL & request
        url = NSURL(string: "http://dev.qa.switchit001.com/dev2/request.php?msgId=2&session=0016nMfm&par0=title&par1=2&par2=description&par3=1")
        
        self.thumbnail.image = manager.thumbnailImage //transfer the thumbnail image to display on this VC
        self.textView.delegate = self //to use all of the textview delegate methods
        
        //Placeholder initial
        self.textView.text = "What's your question?"
        self.textView.textColor = UIColor.lightGrayColor()
        
    }
    
    
    
    
    //customize the navigation bar
    override func viewDidAppear(animated: Bool) {

        var nav = self.navigationController?.navigationBar
        
        nav?.tintColor = UIColor.whiteColor()

        let imageView = UIImageView(frame: CGRect(x: 0, y: 0, width: 40, height: 40))
        imageView.contentMode = .ScaleAspectFit

        let image = UIImage(named: "NavBarLogo")
        imageView.image = image

        navigationItem.titleView = imageView
    }
    
    
    
    //send the question to the server
    //Use the Question Manager to send
    @IBAction func sendQuestion(sender: UIButton){
        // make title -- get that
        // Grab whateve is in the Text View
        // send arbitrary category

        
        var request: NSMutableURLRequest?
        let HTTPMethod: String = "POST"
        var timeoutInterval: NSTimeInterval = 60
        var HTTPShouldHandleCookies: Bool = false
        var imageData :NSData = UIImageJPEGRepresentation(manager.thumbnailImage, 1.0);
        
        request = NSMutableURLRequest(URL: url)
        request!.HTTPMethod = HTTPMethod
        request!.timeoutInterval = timeoutInterval
        request!.HTTPShouldHandleCookies = HTTPShouldHandleCookies
        
        
        let boundary = "----------SwIfTeRhTtPrEqUeStBoUnDaRy"
        let contentType = "multipart/form-data; boundary=\(boundary)"
        request!.setValue(contentType, forHTTPHeaderField:"Content-Type")
        var body = NSMutableData();
        
        
        let tempData = NSMutableData()
        let fileName = "niccage.jpg"
        let parameterName = "file0"
        
        
        let mimeType = "application/octet-stream"
        
        tempData.appendData("--\(boundary)\r\n".dataUsingEncoding(NSUTF8StringEncoding)!)
        let fileNameContentDisposition = "filename=\"\(fileName)\""
        let contentDisposition = "Content-Disposition: form-data; name=\"\(parameterName)\"; \(fileNameContentDisposition)\r\n"
        tempData.appendData(contentDisposition.dataUsingEncoding(NSUTF8StringEncoding)!)
        tempData.appendData("Content-Type: \(mimeType)\r\n\r\n".dataUsingEncoding(NSUTF8StringEncoding)!)
        tempData.appendData(imageData)
        tempData.appendData("\r\n".dataUsingEncoding(NSUTF8StringEncoding)!)
        
        body.appendData(tempData)
        
        body.appendData("\r\n--\(boundary)--\r\n".dataUsingEncoding(NSUTF8StringEncoding)!)
        
        request!.setValue("\(body.length)", forHTTPHeaderField: "Content-Length")
        request!.HTTPBody = body
        
        
        
        var vl_error :NSErrorPointer = nil
        var responseData  = NSURLConnection.sendSynchronousRequest(request!,returningResponse: nil, error:vl_error)
        
        var results = NSString(data:responseData!, encoding:NSUTF8StringEncoding)
        println("finish \(results)")
    
    }
    
    
    
    
    //If user hits the return key --> get rid of the keyboard
    func textView(textView: UITextView!, shouldChangeTextInRange: NSRange, replacementText: NSString!) {
        if(replacementText == "\n") {
            textView.resignFirstResponder()
        }
    }

    override func touchesBegan(touches: NSSet, withEvent event: UIEvent) {
        self.textView.endEditing(true)
    }
    
    
    
    
    //Placeholder text
    func textViewDidBeginEditing(textView: UITextView) {
        if textView.text == "What's your question?"{
                textView.text = ""
                textView.textColor = UIColor.blackColor()
        }
        textView.becomeFirstResponder()
    }
    
    func textViewDidEndEditing(textView: UITextView) {
        if textView.text == ""{
            textView.text = "What's your question?"
            textView.textColor = UIColor.lightGrayColor()
        }
        textView.resignFirstResponder()
    }
}
