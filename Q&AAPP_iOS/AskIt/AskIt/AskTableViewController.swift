//
//  AskTableViewController.swift
//  AskIt
//
//  Created by Komran Ghahremani on 12/27/14.
//  Copyright (c) 2014 Komreezy. All rights reserved.
//

import UIKit


//extensions to allow the use of hexadecimal numbers
extension UIColor {
    
    convenience init(hex: Int) {
        
        let components = (
            R: CGFloat((hex >> 16) & 0xff) / 255,
            G: CGFloat((hex >> 08) & 0xff) / 255,
            B: CGFloat((hex >> 00) & 0xff) / 255
        )
        
        self.init(red: components.R, green: components.G, blue: components.B, alpha: 1)
        
    }
    
}
extension CGColor {
    
    class func colorWithHex(hex: Int) -> CGColorRef {
        
        return UIColor(hex: hex).CGColor
        
    }
    
}


class AskTableViewController: UIViewController, UITableViewDelegate, UITableViewDataSource, UISearchDisplayDelegate, UISearchBarDelegate, UITextViewDelegate {
    
    var url: NSURL!
    var request:NSURLRequest!
    
    @IBOutlet var questionTable: UITableView! //Home Screen -- Question Table to load
    
    //to store the data from the JSON files
    var titleString: String!
    var titleJSON:JSON!
    
    var categoryString: String!
    var categoryJSON:JSON!
    
    var numAnswersInt: Int!
    var numAnswersJSON:JSON!
    
    var lastPageJSON:JSON! //are there any questions left to load
    var lastPage = 0

    var imageThumbnail: UIImage!
    var imageJSON:JSON!
    
    var i = 0 //index to grab questions
    
    var canEdit:Bool = true
    @IBOutlet var table : UITableView!
    @IBOutlet var searchBar: UISearchBar!
    
    let tap = UITapGestureRecognizer()
    
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
        
        /* Conceptually
         * 1. Send server request for first page and load the data with a loop
         * 2. Set up while loop to loop until lastPage == 1
         * 3. Put same request in the loop with par3=1
         * 4. Loop keeps looping on scroll down trigger if lastPage == 0
         */
        
        
        tap.addTarget(self, action: "dismissKeyboard")
        self.view.addGestureRecognizer(tap)
        

        //First server URL & request
        url = NSURL(string: "http://dev.qa.switchit001.com/dev2/request.php?msgId=3&session=0016nMfm&par2=0&par3=0")
        request = NSURLRequest(URL: url!)

        
        //send the first request to the server
        NSURLConnection.sendAsynchronousRequest(request, queue: NSOperationQueue.mainQueue()) {(response, data, error) in
            
            //get the json data
            var json:JSON = JSON(data: data)
            println(json["results"]["numQuestions"])
            
            //see if last page for next request
            self.lastPageJSON = json["results"]["lastPage"]
            self.lastPage = self.lastPageJSON.integerValue!
            
            println(self.lastPage)
            
            //loop through the questions and get the information about each question
            //          --- Will be done in Pages of 10 ---
            //          --- Change to 10 for when we get more questions ---
            for self.i = 0; self.i < 4; self.i++ {
    
                //get question information from the server and put in variables
                self.titleJSON = json["results"]["questions"][self.i]["title"]
                self.categoryJSON = json["results"]["questions"][self.i]["category"]
                self.numAnswersJSON = json["results"]["questions"][self.i]["numAnswers"]
                self.imageJSON = json["results"]["questions"][self.i]["answers"]["thumbnail"]
                
                
                //cast the json values into strings to be put in the Question Manager
                //      --- Later will cast the numAnswers String into an Int ---
                self.titleString = self.titleJSON.stringValue
                self.categoryString = self.categoryJSON.stringValue
                self.numAnswersInt = self.numAnswersJSON.integerValue
                
                println(self.titleString)
                
                
                //send to the question manager
                //manager.addQuestion(self.titleString, categoryParam: self.categoryString, numAnswersParam: self.numAnswersString, thumbnail: manager.thumbnailImage)
                
                manager.addQuestion(self.titleString, categoryParam: self.categoryString, numAnswersParam: self.numAnswersInt, thumbnailParam: manager.thumbnailImage!)
            }
            self.questionTable.reloadData() //refresh the table
        
        }
        
    }

    //Methods to get rid of the keyboard when done using the search bar
    func dismissKeyboard(){
        self.searchBar.resignFirstResponder()
    }
    
    func searchBarSearchButtonClicked(searchBar: UISearchBar){
        self.searchBar.resignFirstResponder()
    }
    
    
    
    //default view controller method
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    
    //Customize the Navigation bar to blue and add the logo in the center
    override func viewDidAppear(animated: Bool) {
        super.viewDidAppear(true)
        
        var nav = self.navigationController?.navigationBar
        
        nav?.tintColor = UIColor.whiteColor()
        
        let imageView = UIImageView(frame: CGRect(x: 0, y: 0, width: 40, height: 40))
        imageView.contentMode = .ScaleAspectFit
        
        let image = UIImage(named: "NavBarLogo")
        imageView.image = image
        
        navigationItem.titleView = imageView
    }
    
    
    
    //send a request to the server method -- NOT USED as of right now
    func sendRequest(){
        //send the first request to the server
        NSURLConnection.sendAsynchronousRequest(request, queue: NSOperationQueue.mainQueue()) {(response, data, error) in
            
            //get the json data
            var json:JSON = JSON(data: data)
            println(json["results"]["numQuestions"])
            
            //see if last page for next request
            self.lastPageJSON = json["results"]["lastPage"]
            self.lastPage = self.lastPageJSON.integerValue!
            
            println(self.lastPage)
            
            //loop through the questions and get the information about each question
            //          --- Will be done in Pages of 10 ---
            //          --- Change to 10 for when we get more questions ---
            for self.i; self.i < 5; self.i++ {
                
                //get question information from the server and put in variables
                self.titleJSON = json["results"]["questions"][self.i]["title"]
                self.categoryJSON = json["results"]["questions"][self.i]["category"]
                self.numAnswersJSON = json["results"]["questions"][self.i]["numAnswers"]
                self.imageJSON = json["results"]["questions"][self.i]["answers"]["thumbnail"]
                
                
                //cast the json values into strings to be put in the Question Manager
                //      --- Later will cast the numAnswers String into an Int ---
                self.titleString = self.titleJSON.stringValue
                self.categoryString = self.categoryJSON.stringValue
                self.numAnswersInt = self.numAnswersJSON.integerValue
                
                println(self.titleString)
                
                
                //send to the question manager
                //manager.addQuestion(self.titleString, categoryParam: self.categoryString, numAnswersParam: self.numAnswersString, thumbnail: manager.thumbnailImage)
                
                manager.addQuestion(self.titleString, categoryParam: self.categoryString, numAnswersParam: self.numAnswersInt, thumbnailParam: manager.thumbnailImage!)
            }
            self.questionTable.reloadData() //refresh the table
            
        }
        self.table.reloadData()
    }
    

    
    
    //Returning to view
    //Reload data will call numberrowsinsectionagain
    override func viewWillAppear(animated: Bool) {
        questionTable.reloadData()
    }
    
    
    
    
    //when the user taps on a row -- take them to the single question view controller
    func tableView(tableView: UITableView!, didSelectRowAtIndexPath indexPath: NSIndexPath!) {
        //method to do something if the user selects a table cell -- For later use
        
        var cell: UITableViewCell = self.tableView(tableView, cellForRowAtIndexPath: indexPath)
        
        
        if let titleLabel = cell.viewWithTag(101) as? UILabel { //3
            manager.currentTitle = titleLabel.text!
        }
        
        self.performSegueWithIdentifier("cellclick", sender: cell)
        
    }

    
    
    
    
    //UITableViewDataSource - Number of rows is the number of items in the array
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int{
        return manager.questions.count
    }
    
    
    //Customizing the cell
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {

        let cell = tableView.dequeueReusableCellWithIdentifier("Cell", forIndexPath: indexPath) as UITableViewCell
        
        var thumbnailImageView = cell.viewWithTag(100) as? UIImageView
        
        thumbnailImageView?.image = UIImage(named: "questionmarksmall")
        
        if let titleLabel = cell.viewWithTag(101) as? UILabel { //3
            titleLabel.text = manager.questions[indexPath.row].title
        }
        if let categoryLabel = cell.viewWithTag(102) as? UILabel { //3
            categoryLabel.text = manager.questions[indexPath.row].category
        }
        if let numAnswersLabel = cell.viewWithTag(103) as? UILabel {
            numAnswersLabel.text = String(manager.questions[indexPath.row].numAnswers)
        }
        
        
        return cell
    }
    
    
    
    //whenever the user scrolls down -- could be used for loading next pages of data
    func scrollViewDidEndDragging(scrollView: UIScrollView, willDecelerate decelerate: Bool) {
    
        var currentOffset = scrollView.contentOffset.y
        var maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        if maximumOffset - currentOffset <= -40 {
            println("reload")
            //sendRequest()
        }
    }
}
