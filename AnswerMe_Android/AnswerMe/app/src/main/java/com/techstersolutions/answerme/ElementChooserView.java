package com.techstersolutions.answerme;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.ImageView;
import android.widget.RelativeLayout;

/**
 * Created by admin on 4/14/15.
 */
public class ElementChooserView extends RelativeLayout {
    ImageView addTextImg, addCameraImg, addGalleryImg;

    public ElementChooserView(Context context) {
        super(context);
        init();
    }

    public ElementChooserView(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    public ElementChooserView(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        init();
    }

    private void init() {
        inflate(getContext(), R.layout.dialog_add_item, this);
        addTextImg = (ImageView) findViewById(R.id.add_text_button);
        addCameraImg = (ImageView) findViewById(R.id.add_camera_button);
        addGalleryImg = (ImageView) findViewById(R.id.add_gallery_button);

    }

}
