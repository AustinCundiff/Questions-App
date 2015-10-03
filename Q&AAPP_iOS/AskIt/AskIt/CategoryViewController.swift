//
//  CategoryViewController.swift
//  AskIt
//
//  Created by Komran Ghahremani on 1/12/15.
//  Copyright (c) 2015 Komreezy. All rights reserved.
//

import UIKit

class CategoryViewController: UIViewController {

    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    //dismiss the modal view controller -- hooked up to the done button
    @IBAction func done(sender: AnyObject) {
        self.dismissViewControllerAnimated(true, completion: nil)
        println("done")
    }
}
