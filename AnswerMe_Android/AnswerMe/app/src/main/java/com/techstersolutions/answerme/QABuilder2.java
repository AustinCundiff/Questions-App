package com.techstersolutions.answerme;

import android.app.Dialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Typeface;
import android.graphics.drawable.ShapeDrawable;
import android.graphics.drawable.shapes.RectShape;
import android.net.Uri;
import android.support.v4.app.FragmentActivity;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import java.io.FileNotFoundException;
import java.io.InputStream;
import java.util.ArrayList;

import de.hdodenhof.circleimageview.CircleImageView;
import uk.co.senab.photoview.PhotoViewAttacher;


public class QABuilder2 extends ActionBarActivity {
    LinearLayout circle_holder;
    LinearLayout.LayoutParams circle_params;
    ScrollView scrollView;
    LinearLayout questionDataLayout;
    ArrayList<CircleImageView> circleIndex;
    ArrayList<QData> elements;
    CircleImageView plusButton;
    CircleImageView.OnClickListener circleClickListener;
    View.OnClickListener questionItemClickListener;
    View.OnLongClickListener questionItemLongClickListener;

    RectShape rect;
    ShapeDrawable rectShapeDrawable;
    Paint paint;

    int selectedIndex = 0;
    int chooserIndex;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_qabuilder2);

        rect = new RectShape();
        rectShapeDrawable = new ShapeDrawable(rect);

        paint = rectShapeDrawable.getPaint();
        paint.setColor(Color.WHITE);
        paint.setStyle(Paint.Style.STROKE);
        paint.setStrokeWidth(20);

        circleClickListener = new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                int index = circleIndex.indexOf((CircleImageView) v);
                scrollView.smoothScrollTo(0, questionDataLayout.getChildAt(index).getTop());

                //reset colors/boxes
                for(int i = 0; i < elements.size(); i++) {
                    circleIndex.get(i).setBorderColor(getResources().getColor(R.color.blue));
                    questionDataLayout.getChildAt(i).setBackground(null);
                }
                circleIndex.get(index).setBorderColor(Color.WHITE);
                //TODO: WORKAROUND FOR API 16 CALL BELOW
                questionDataLayout.getChildAt(index).setBackground(rectShapeDrawable);
                selectedIndex = index;
            }
        };

        questionItemClickListener = new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                int index = questionDataLayout.indexOfChild(v);

                //reset colors/boxes
                for(int i = 0; i < elements.size(); i++) {
                    circleIndex.get(i).setBorderColor(getResources().getColor(R.color.blue));
                    questionDataLayout.getChildAt(i).setBackground(null);
                }
                circleIndex.get(index).setBorderColor(Color.WHITE);
                //TODO: WORKAROUND FOR API 16 CALL BELOW
                v.setBackground(rectShapeDrawable);
                selectedIndex = index;

            }
        };

        questionItemLongClickListener = new View.OnLongClickListener() {
            @Override
            public boolean onLongClick(View v) {
                int index = questionDataLayout.indexOfChild(v);
                if(elements.get(index).isText()) {
                    Toast.makeText(getApplicationContext(), "EDIT TEXT", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(getApplicationContext(), "EDIT IMAGE", Toast.LENGTH_SHORT).show();
                }
                return true;
            }
        };
        questionDataLayout = (LinearLayout) findViewById(R.id.question_data);


        elements = new ArrayList<QData>();

        scrollView = (ScrollView) findViewById(R.id.scrollview);

        circleIndex = new ArrayList<CircleImageView>();
        circle_holder = (LinearLayout) findViewById(R.id.circle_holder);
        circle_params = new LinearLayout.LayoutParams(140, ViewGroup.LayoutParams.MATCH_PARENT);

        elements.add(new QData("Hello"));
        elements.add(new QData("Goodbye"));
        elements.add(new QData(BitmapFactory.decodeResource(getResources(), R.drawable.ic_launcher)));

        plusButton = (CircleImageView) findViewById(R.id.plus_button);
        plusButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                final ElementChooserView chooser = new ElementChooserView(getApplicationContext());
//                selectedIndex = questionDataLayout.indexOfChild(chooser);
                chooser.addTextImg.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        int index = questionDataLayout.indexOfChild(chooser);
                        questionDataLayout.removeViewAt(index);
                        elements.add(index, new QData("New Text Item -- Long Click to Edit"));
                        addElementCircles();
                        createScrollView();


                    }
                });
                chooser.addCameraImg.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        //TODO: get camera stuff
                        chooserIndex = questionDataLayout.indexOfChild(chooser);
                        Intent i = new Intent(getApplicationContext(), CameraActivity.class);
                        startActivityForResult(i, 1);
                    }
                });
                chooser.addGalleryImg.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        chooserIndex = questionDataLayout.indexOfChild(chooser);
                        Intent i = new Intent(Intent.ACTION_PICK);
                        i.setType("image/*");
                        startActivityForResult(i, 2);


                    }
                });
//                questionDataLayout.addView(chooser);
                questionDataLayout.addView(chooser, selectedIndex+1);
                scrollView.smoothScrollTo(0, questionDataLayout.getChildAt(selectedIndex+1).getTop());
            }
        });


        addElementCircles();
        createScrollView();

    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_qabuilder2, menu);
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


    public void addElementCircles() {
        CircleImageView civ;
        circle_holder.removeAllViewsInLayout();
        circleIndex.clear();
        QData qData;
        for(int i = 0; i < elements.size(); i++) {
            qData = elements.get(i);
            if (qData.isText()) {
                civ = new CircleImageView(this);
                civ.setImageDrawable(getResources().getDrawable(R.drawable.text));
                civ.setBorderColor(getResources().getColor(R.color.blue));
                civ.setBorderWidth(10);
                civ.setOnClickListener(circleClickListener);
                circle_holder.addView(civ, circle_params);
                circleIndex.add(civ);
            } else if (!qData.isText()) {
                civ = new CircleImageView(this);
//                byte[] array = fragments.get(i).mData.getImage();
                civ.setImageBitmap(elements.get(i).getImageBitmap());
                civ.setBorderColor(getResources().getColor(R.color.blue));
                civ.setBorderWidth(10);
                civ.setOnClickListener(circleClickListener);
                circle_holder.addView(civ, circle_params);
                circleIndex.add(civ);
            }
            if (i == 0) circleIndex.get(i).setBorderColor(Color.WHITE);
        }
    }

    public void createScrollView() {
        //LayoutParams (Reuseable)
        LinearLayout.LayoutParams lparams_text = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.MATCH_PARENT);
        LinearLayout.LayoutParams lparams_img = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.MATCH_PARENT, 1000);

        RelativeLayout.LayoutParams rparams_text = new RelativeLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
        RelativeLayout.LayoutParams rparams_img = new RelativeLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT, 1000);

        lparams_text.gravity = Gravity.CENTER_HORIZONTAL;
        lparams_img.gravity = Gravity.CENTER_HORIZONTAL;

        if(questionDataLayout.getChildCount() != 0) {
            questionDataLayout.removeAllViewsInLayout();
        }
        QData data;
        for(int position = 0; position < elements.size(); position++) {
            data = elements.get(position);
            RelativeLayout rl = new RelativeLayout(getApplicationContext());

            if(data.isText()) {
                TextView tv = new TextView(this);
                tv.setLayoutParams(lparams_text);
                tv.setText(data.getText());
                tv.setTextSize(20);
                tv.setTextColor(Color.DKGRAY);
                tv.setOnClickListener(questionItemClickListener);
                tv.setOnLongClickListener(questionItemLongClickListener);
                rl.addView(tv, rparams_text);
                questionDataLayout.addView(rl, lparams_text);
            }
            else if (!data.isText()) {
                //ADD IMAGEVIEW
                ImageKeepAspectRatio iv = new ImageKeepAspectRatio(this);
                iv.setLayoutParams(lparams_img);
                iv.setImageBitmap(data.getImageBitmap());

                PhotoViewAttacher photoViewAttacher = new PhotoViewAttacher(iv);
                photoViewAttacher.setOnLongClickListener(questionItemLongClickListener);
                photoViewAttacher.setOnViewTapListener(new PhotoViewAttacher.OnViewTapListener() {
                    @Override
                    public void onViewTap(View view, float x, float y) {
                        int index = questionDataLayout.indexOfChild(view);
//                           scrollView.scrollTo(0, questionDataLayout.getChildAt(index).getTop());

                        //reset colors/boxes
                        for(int i = 0; i < elements.size(); i++) {
                            circleIndex.get(i).setBorderColor(getResources().getColor(R.color.blue));
                            questionDataLayout.getChildAt(i).setBackground(null);
                        }
//                        circleIndex.get(index).setBorderColor(Color.WHITE);
                        //TODO: WORKAROUND FOR API 16 CALL BELOW
                        view.setBackground(rectShapeDrawable);
                        selectedIndex = index;
                    }
                });
                photoViewAttacher.update();


                rl.addView(iv, rparams_img);
                questionDataLayout.addView(rl, lparams_img);
            }
        }
    }




    protected void onActivityResult(int requestCode, int resultCode, Intent data) {

        LinearLayout.LayoutParams lparams_img = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.MATCH_PARENT, 1000);
        lparams_img.gravity = Gravity.CENTER_HORIZONTAL;
        ImageKeepAspectRatio iv = new ImageKeepAspectRatio(this);
        iv.setLayoutParams(lparams_img);

        Log.d("QA_APP", "RETURN...");
        if (requestCode == 1) {
            if(resultCode == RESULT_OK){

                //A LOT OF RE-WRITTEN CODE THAT MUST BE REFACTORED
                Log.d("QA_APP", "RETURN SUCCESS");
                questionDataLayout.removeViewAt(chooserIndex);
                Bitmap tempBitmap = PicUtil.loadFromCacheFile();

                iv.setImageBitmap(PicUtil.loadFromCacheFile());

                PhotoViewAttacher photoViewAttacher = new PhotoViewAttacher(iv);
                photoViewAttacher.setOnLongClickListener(questionItemLongClickListener);
                photoViewAttacher.setOnViewTapListener(new PhotoViewAttacher.OnViewTapListener() {
                    @Override
                    public void onViewTap(View view, float x, float y) {
                        int index = questionDataLayout.indexOfChild(view);
//                           scrollView.scrollTo(0, questionDataLayout.getChildAt(index).getTop());

                        //reset colors/boxes
                        for (int i = 0; i < elements.size(); i++) {
                            circleIndex.get(i).setBorderColor(getResources().getColor(R.color.blue));
                            questionDataLayout.getChildAt(i).setBackground(null);
                        }
                        circleIndex.get(index).setBorderColor(Color.WHITE);
                        //TODO: WORKAROUND FOR API 16 CALL BELOW
                        questionDataLayout.getChildAt(index).setBackground(rectShapeDrawable);
                        selectedIndex = index;
                    }
                });
                photoViewAttacher.update();

                elements.add(new QData(tempBitmap));

                questionDataLayout.addView(iv);
                addElementCircles();
                createScrollView();
            }
            if (resultCode == RESULT_CANCELED) {
                Log.d("QA_APP", "RETURN FAIL");
            }
        } else if(requestCode == 2) {
            if(resultCode == RESULT_OK) {
                Uri selectedImage = data.getData();
                InputStream imageStream = null;
                try {
                    imageStream = getContentResolver().openInputStream(selectedImage);
                } catch (FileNotFoundException e) {
                    e.printStackTrace();
                }
                Bitmap selectedImg = BitmapFactory.decodeStream(imageStream);
                iv.setImageBitmap(selectedImg);
                PhotoViewAttacher photoViewAttacher = new PhotoViewAttacher(iv);
                photoViewAttacher.setOnLongClickListener(questionItemLongClickListener);
                photoViewAttacher.setOnViewTapListener(new PhotoViewAttacher.OnViewTapListener() {
                    @Override
                    public void onViewTap(View view, float x, float y) {
                        int index = questionDataLayout.indexOfChild(view);
//                           scrollView.scrollTo(0, questionDataLayout.getChildAt(index).getTop());

                        //reset colors/boxes
                        for (int i = 0; i < elements.size(); i++) {
                            circleIndex.get(i).setBorderColor(getResources().getColor(R.color.blue));
                            questionDataLayout.getChildAt(i).setBackground(null);
                        }
                        circleIndex.get(index).setBorderColor(Color.WHITE);
                        //TODO: WORKAROUND FOR API 16 CALL BELOW
                        questionDataLayout.getChildAt(index).setBackground(rectShapeDrawable);
                        selectedIndex = index;
                    }
                });
                photoViewAttacher.update();

                elements.add(new QData(selectedImg));

                addElementCircles();
                createScrollView();
            }
        }

    }//onActivityResult
}
