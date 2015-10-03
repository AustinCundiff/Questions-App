//
//  AskViewController.swift
//  AskIt
//
//  Created by Komran Ghahremani on 12/27/14.
//  Copyright (c) 2014 Komreezy. All rights reserved.
//

import UIKit
import AVFoundation

class AskViewController: UIViewController, UIImagePickerControllerDelegate, UINavigationControllerDelegate {
    
    @IBOutlet var imageView: UIImageView!
    @IBOutlet var image: UIImage!
    var picker = UIImagePickerController()
    
    
    //set the default blue question mark for no image
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.imageView.image = manager.thumbnailImage
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
    
    
    //open up the camera
    @IBAction func takePhoto(sender: UIButton) {
        
        picker.delegate = self
        picker.allowsEditing = true
        picker.sourceType = UIImagePickerControllerSourceType.Camera
        
        self.presentViewController(picker, animated: true, completion: nil)
    }
    
    //open up the photo library
    @IBAction func choosePhoto(sender: UIButton){
        picker.delegate = self
        picker.allowsEditing = true
        picker.sourceType = UIImagePickerControllerSourceType.PhotoLibrary
        
        self.presentViewController(picker, animated: true, completion: nil)
    }
    
    
    func imagePickerController(picker: UIImagePickerController!, didFinishPickingMediaWithInfo info:NSDictionary!) {
        /*
        let tempImage = info[UIImagePickerControllerOriginalImage] as UIImage
        self.image = tempImage
        
        self.dismissViewControllerAnimated(true, completion: nil)
        */
        
        let tempImage = info[UIImagePickerControllerOriginalImage] as UIImage
        imageView.image  = tempImage
        manager.thumbnailImage = tempImage
        
        self.dismissViewControllerAnimated(true, completion: nil)
    }

    
    func imagePickerControllerDidCancel(picker: UIImagePickerController) {
        self.dismissViewControllerAnimated(true, completion: nil)
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

}
