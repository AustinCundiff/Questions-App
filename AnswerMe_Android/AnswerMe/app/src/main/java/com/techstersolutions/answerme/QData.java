package com.techstersolutions.answerme;

import android.graphics.Bitmap;
import android.os.Parcel;
import android.os.Parcelable;

import java.io.ByteArrayOutputStream;
import java.io.Serializable;

/**
 * Created by admin on 3/11/15.
 */
public class QData implements Serializable {
    private String text;
    private byte[] image;
    private Bitmap bitmap;
    private boolean isTextData;

    public QData(String text) {
        this.text = text;
        isTextData = true;
    }

    public QData(Bitmap image) {
        //make into byte[] for serialization
        ByteArrayOutputStream stream = new ByteArrayOutputStream();
        image.compress(Bitmap.CompressFormat.PNG, 100, stream);
        this.image = stream.toByteArray();

        this.bitmap = image;

        isTextData = false;
    }

    public QData(byte[] image) {
        this.image = image;

        isTextData = false;
    }

    public boolean isText() {
        return isTextData;
    }

    public String getText() {
        return this.text;
    }

    public byte[] getImage() {
        return this.image;
    }

    public Bitmap getImageBitmap() {
        return this.bitmap;
    }

}
