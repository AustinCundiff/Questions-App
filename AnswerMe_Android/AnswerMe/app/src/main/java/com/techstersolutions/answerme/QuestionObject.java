/**
 * QuestionObject.java
 * Techster Solutions
 * Jason John
 */
package com.techstersolutions.answerme;

import android.graphics.Bitmap;
import android.os.Parcel;
import android.os.Parcelable;
import android.widget.ImageView;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Objects;

public class QuestionObject implements Serializable {
    private int id, category, numAnswers;
    private String title, thumbUrl;
    private Bitmap thumbnail;

    public QuestionObject() {
        id = -1;
        category = -1;
        numAnswers = -1;
        title = "ERR";
        thumbUrl = "ERR";
        thumbnail = null;
    }

    public QuestionObject(int id, int category, int numAnswers, String title, Bitmap thumbnail) {
        this.id = id;
        this.category = category;
        this.numAnswers = numAnswers;
        this.title = title;
        this.thumbnail = thumbnail;
        this.thumbUrl = "NONE_SPECIFIED";
    }

    public QuestionObject(int id, int category, int numAnswers, String title, String thumbUrl) {
        this.id = id;
        this.category = category;
        this.numAnswers = numAnswers;
        this.title = title;
        this.thumbUrl = thumbUrl;

        //get bitmap from thumb url
        //this.thumbnail = *magic trick*
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getCategory() {
        return category;
    }

    public void setCategory(int category) {
        this.category = category;
    }

    public int getNumAnswers() {
        return numAnswers;
    }

    public void setNumAnswers(int numAnswers) {
        this.numAnswers = numAnswers;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getThumbUrl() {
        return thumbUrl;
    }

    public void setThumbUrl(String thumbUrl) {
        this.thumbUrl = thumbUrl;
    }

    public Bitmap getThumbnail() {
        return thumbnail;
    }

    public void setThumbnail(Bitmap thumbnail) {
        this.thumbnail = thumbnail;
    }
}