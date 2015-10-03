package com.techstersolutions.answerme;

import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Typeface;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;

import uk.co.senab.photoview.PhotoViewAttacher;


public class QuestionPreviewActivity extends ActionBarActivity {
    public static final String TAG = "QA_APP";
    ArrayList<QData> previewElements;
    LinearLayout layout;
    ScrollView scrollView;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_question_preview);
        Intent intent = getIntent();
        if(intent != null && intent.hasExtra("preview")) {
            previewElements = (ArrayList<QData>) intent.getSerializableExtra("preview");
            Toast.makeText(this, "Size: " + previewElements.size(), Toast.LENGTH_SHORT).show();
        }
        scrollView = (ScrollView) findViewById(R.id.scrollview);

        buildQuestion();


    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_question_preview, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    private void buildQuestion() {
        Log.d(TAG, "Num Parsed Question Elements: " + previewElements.size());

        //LayoutParams (Reuseable)
        LinearLayout.LayoutParams lparams_text = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        LinearLayout.LayoutParams lparams_img = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        lparams_text.gravity = Gravity.CENTER_HORIZONTAL;
        lparams_img.gravity = Gravity.CENTER_HORIZONTAL;

        layout = (LinearLayout) findViewById(R.id.question_data);

        for(int position = 0; position < previewElements.size(); position++) {
            if(position == 0) {
                //FIRST INDEX IS QUESTION TITLE

                TextView tv=new TextView(this);
                tv.setLayoutParams(lparams_text);
                tv.setText((String) previewElements.get(position).getText());
                tv.setTextSize(30);
                tv.setTypeface(tv.getTypeface(), Typeface.BOLD);
                tv.setTextColor(Color.BLACK);

                layout.addView(tv);
            }
            else {
                if(previewElements.get(position).isText()) {
                    TextView tv = new TextView(this);
                    tv.setLayoutParams(lparams_text);
                    tv.setText((String) previewElements.get(position).getText());
                    tv.setTextSize(20);
                    tv.setTextColor(Color.DKGRAY);


                    layout.addView(tv);
                }
                else if (!previewElements.get(position).isText()) {
                    //ADD IMAGEVIEW
                    ImageKeepAspectRatio iv = new ImageKeepAspectRatio(this);
                    iv.setLayoutParams(lparams_img);
                    Bitmap b = BitmapFactory.decodeByteArray(previewElements.get(position).getImage(), 0,
                            previewElements.get(position).getImage().length);
                    iv.setImageBitmap(b);

                    PhotoViewAttacher photoViewAttacher = new PhotoViewAttacher(iv);
                    photoViewAttacher.update();

                    layout.addView(iv);
                }
            }
        }


    }
}
