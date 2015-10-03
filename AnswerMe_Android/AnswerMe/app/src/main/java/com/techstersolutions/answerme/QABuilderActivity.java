package com.techstersolutions.answerme;

import android.app.Activity;
import android.app.FragmentTransaction;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;


import com.nhaarman.listviewanimations.ArrayAdapter;
import com.nhaarman.listviewanimations.itemmanipulation.DynamicListView;
import com.nhaarman.listviewanimations.itemmanipulation.dragdrop.OnItemMovedListener;
import com.nhaarman.listviewanimations.itemmanipulation.dragdrop.TouchViewDraggableManager;

import org.w3c.dom.Text;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectOutputStream;
import java.util.ArrayList;

import uk.co.senab.photoview.PhotoViewAttacher;


public class QABuilderActivity extends ActionBarActivity {
    DynamicListView listView;
    ArrayList<QData> buildElements;
    Button addButton, previewButton;
    LinearLayout layout;
    ArrayList<QData> previewElements;
    public static final String TAG = "QA_APP";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_qabuilder);
        /******************************************/

        final PreviewFragment fragment = (PreviewFragment) getSupportFragmentManager()
                .findFragmentById(R.id.fragment1);
        fragment.getView().setVisibility(View.GONE);


        listView = (DynamicListView) findViewById(R.id.qa_builder_listview);
        previewButton = (Button) findViewById(R.id.preview_button);
        buildElements = new ArrayList<QData>();
        buildElements.add(new QData("Hello"));
        buildElements.add(new QData("--asd---"));
        buildElements.add(new QData("--asdsdfsdf---"));
        buildElements.add(new QData(BitmapFactory.decodeResource(getResources(), R.drawable.ic_launcher)));


        final ArrayAdapter<QData> adapter = new MyListAdapter(this, buildElements);

        listView.setAdapter(adapter);
        listView.enableDragAndDrop();
        listView.setDraggableManager(new TouchViewDraggableManager(R.id.list_row_draganddrop_touchview));
        listView.setOnItemMovedListener(new MyOnItemMovedListener(adapter));
        listView.setOnItemLongClickListener(new MyOnItemLongClickListener(listView));

        previewButton.setOnTouchListener(new View.OnTouchListener() {

            /* Construct preview Fragment */
            public boolean onTouch(View v, MotionEvent event) {

                switch (event.getAction() & MotionEvent.ACTION_MASK) {

                    case MotionEvent.ACTION_DOWN:
                        v.setPressed(true);
                        fragment.getView().setVisibility(View.VISIBLE);


                        Intent intent = new Intent(getApplicationContext(), QuestionPreviewActivity.class);
                        previewElements = new ArrayList<QData>();
                        for (int i = 0; i < buildElements.size(); i++) {
                            if (buildElements.get(i).isText()) {
                                previewElements.add(new QData(buildElements.get(i).getText()));
                            } else {
                                previewElements.add(new QData(buildElements.get(i).getImage()));
                            }
                        }
                        buildQuestion(); // Start action ...
                        break;
                    case MotionEvent.ACTION_UP:
                    case MotionEvent.ACTION_OUTSIDE:
                    case MotionEvent.ACTION_CANCEL:
                        v.setPressed(false);
                        fragment.getView().setVisibility(View.GONE);
                        ((FrameLayout)fragment.getView()).removeAllViewsInLayout();
                        // Stop action ...
                        break;
                    case MotionEvent.ACTION_POINTER_DOWN:
                        break;
                    case MotionEvent.ACTION_POINTER_UP:
                        break;
                    case MotionEvent.ACTION_MOVE:
                        break;
                }

                return true;
            }


        });
        /******************************************/
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_qabuilder, menu);
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


    private static class MyListAdapter extends ArrayAdapter<QData> {
        private QData listObj;
        private final Context mContext;
        private int type = 0;
        ViewHolder viewHolder;
        LinearLayout layout;
        LinearLayout.LayoutParams params;

        MyListAdapter(final Context context, ArrayList<QData> list) {
            mContext = context;
            for (int i = 0; i < list.size(); i++) {
                add(list.get(i));
            }
            params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
        }

        @Override
        public long getItemId(final int position) {
            return getItem(position).hashCode();
        }

        @Override
        public boolean hasStableIds() {
            return true;
        }
        Toast theToast;

        @Override
        public View getView(final int position, final View convertView, final ViewGroup parent) {
            View view = convertView;


            if (view == null) {
                view = LayoutInflater.from(mContext).inflate(R.layout.qabuilder_element, parent, false);
                viewHolder = new ViewHolder();
                viewHolder.tv = (TextView) view.findViewById(R.id.qabuild_txt);
                viewHolder.iv = (ImageView) view.findViewById(R.id.qabuild_img);
            }


            listObj = getItem(position);

            if(listObj.isText())
            {
                viewHolder.tv.setText(listObj.getText());
//                iv.setVisibility(View.GONE);
            }
            else if(!listObj.isText())
            {
               viewHolder.iv.setImageBitmap(
                        BitmapFactory.decodeByteArray(listObj.getImage(), 0, listObj.getImage().length));
//                tv.setVisibility(View.GONE);
            }

          return view;

        }

        private class ViewHolder {
            TextView tv = null;
            ImageView iv = null;
        }
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

        layout = (LinearLayout) findViewById(R.id.preview_data);

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

    private class MyOnItemMovedListener implements OnItemMovedListener {

        private final ArrayAdapter<QData> mAdapter;

        private Toast mToast;

        MyOnItemMovedListener(final ArrayAdapter<QData> adapter) {
            mAdapter = adapter;
        }

        @Override
        public void onItemMoved(final int originalPosition, final int newPosition) {
            if (mToast != null) {
                mToast.cancel();
            }

            mToast = Toast.makeText(getApplicationContext(), getString(R.string.moved, mAdapter.getItem(newPosition), newPosition), Toast.LENGTH_SHORT);
            mToast.show();
        }
    }
    private static class MyOnItemLongClickListener implements AdapterView.OnItemLongClickListener {

        private final DynamicListView mListView;

        MyOnItemLongClickListener(final DynamicListView listView) {
            mListView = listView;
        }

        @Override
        public boolean onItemLongClick(final AdapterView<?> parent, final View view, final int position, final long id) {
            if (mListView != null) {
                mListView.startDragging(position - mListView.getHeaderViewsCount());
            }
            return true;
        }
    }



}


