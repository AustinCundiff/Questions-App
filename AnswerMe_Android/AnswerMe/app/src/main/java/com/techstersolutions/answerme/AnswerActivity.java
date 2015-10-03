package com.techstersolutions.answerme;

import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.LinearGradient;
import android.graphics.Typeface;
import android.os.Build;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.AbsoluteLayout;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.OptionalDataException;
import java.util.ArrayList;
import java.util.Objects;

import uk.co.senab.photoview.PhotoView;
import uk.co.senab.photoview.PhotoViewAttacher;

/**
 * PARSED QUESTION LIST ORDER
 * 0: Title
 * 1-n: Question Body
 *
 * When building, use a for loop and dynamically build the question
 * based on the ordering of the question, as described by the user.
 */
public class AnswerActivity extends ActionBarActivity {
    public static final String TAG = "QA_APP";
    QuestionObject questionObject;
    ArrayList<Object> parsedQuestionList;
    ArrayList<Object> answerList;
    ImageView questionImage;
    LinearLayout layout;
    Toolbar toolbar;
    ListView answerListView;
    AnswerListAdapter answerListAdapter;
    String sessionKey, url;
    public static final String ROOT_ADDR = "http://dev.qa.switchit001.com/dev2/request.php";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_answer);


        //checking API version for statusbar styling
        int currentapiVersion = android.os.Build.VERSION.SDK_INT;
        if (currentapiVersion >= Build.VERSION_CODES.LOLLIPOP){
            getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

            getWindow().setStatusBarColor(getResources().getColor(R.color.primaryColor));
            // Do something for froyo and above versions
            // What would we do? - Jason
        }
        Toolbar toolbar = (Toolbar)findViewById(R.id.app_bar);
        if (toolbar != null) {
            setSupportActionBar(toolbar);

            toolbar.setNavigationIcon(R.drawable.homeicon);
            //toolbar.setTitle("home");
        }

        Bundle extras = getIntent().getExtras();
        if (extras != null)
        {
            String myParam = extras.getString("QuestionID");
        }
        else
        {
            //..oops!
        }

        sessionKey = getSharedPreferences("com.switchit001.qaapp", Context.MODE_PRIVATE).getString("sessionKey", "NULL");

//Building URL for HTTP Request
        url = ROOT_ADDR
                + "?session=" + sessionKey
                + "&msgId=" + 3
                + "&par0=" + 1
                + "&par1=" //optional search string
                + "&par2=" + 1
                + "&par3=" + 0;


        answerListView = (ListView) findViewById(R.id.question_detail_answer_list);
        answerListAdapter = new AnswerListAdapter(this, R.layout.list_row);
        answerList = new ArrayList<Object>();

        //DUMMY ANSWER DATA
        answerList.add(1);
        answerList.add(1);
        answerList.add(2); //Represent user's answer for a question (Green)
        answerList.add(1);
        answerList.add(1);
        answerList.add(1);
        answerList.add(1);
        answerList.add(1);
        answerListAdapter.addAll(answerList);
        answerListView.setAdapter(answerListAdapter);
        answerListAdapter.notifyDataSetChanged();


        if(getIntent().hasExtra("preview")) {
            //unparse byte array of elements
            Log.i("QA_APP", "PREVIEW!");
            ObjectInputStream ois = null;
            try {
                ois = new ObjectInputStream(new ByteArrayInputStream(getIntent().getByteArrayExtra("preview")));
            } catch (IOException e) {
                e.printStackTrace();
            }
            try {
                parsedQuestionList = (ArrayList<Object>) ois.readObject();
            } catch (ClassNotFoundException e) {
                e.printStackTrace();
            } catch (OptionalDataException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
            } finally {
                try {
                    ois.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        } else if(getIntent().hasExtra("question")) {
            Log.d(TAG, "Found Extra");
            questionObject = getIntent().getParcelableExtra("question");
            parsedQuestionList = new ArrayList<Object>();
            makeDummyQuestion();
            buildQuestion();
        } else {
            Log.e(TAG, "No extra?");
        }
    }


    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_answer, menu);
        return true;
    }

    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }
        if(id==android.R.id.home){
            startActivity(new Intent(getApplicationContext(), QuestionListActivity.class));//NavUtils.navigateUpFromSameTask(this);
        }
        return super.onOptionsItemSelected(item);
    }
    private void generateQuestionUi(ArrayList<Object> list) {

    }

    private void makeDummyQuestion() {
        parsedQuestionList.add("Techster Dummy Question");
        //parsedQuestionList.add(BitmapFactory.decodeResource(getResources(), R.drawable.splash));
        parsedQuestionList.add("TDHFJGYUHIJ");

        parsedQuestionList.add("What is Techster Solutions?");
        parsedQuestionList.add(BitmapFactory.decodeResource(getResources(), R.drawable.ic_launcher));
        parsedQuestionList.add("\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut" +
                " labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip" +
                " ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat " +
                "nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id " +
                "est laborum.\"");
        parsedQuestionList.add(BitmapFactory.decodeResource(getResources(), R.drawable.ic_launcher));
    }

    private void buildQuestion() {
        Log.d(TAG, "Num Parsed Question Elements: " + parsedQuestionList.size());

        //LayoutParams (Reuseable)
        LinearLayout.LayoutParams lparams_text = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        LinearLayout.LayoutParams lparams_img = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        lparams_text.gravity = Gravity.CENTER_HORIZONTAL;
        lparams_img.gravity = Gravity.CENTER_HORIZONTAL;

        layout = (LinearLayout) findViewById(R.id.question_data);

        for(int position = 0; position < parsedQuestionList.size(); position++) {
            if(position == 0) {
                //FIRST INDEX IS QUESTION TITLE

                TextView tv=new TextView(this);
                tv.setLayoutParams(lparams_text);
                tv.setText((String) parsedQuestionList.get(position));
                tv.setTextSize(30);
                tv.setTypeface(tv.getTypeface(), Typeface.BOLD);
                tv.setTextColor(Color.BLACK);

                layout.addView(tv);
            }
            else {
                if(parsedQuestionList.get(position) instanceof String) {
                    TextView tv = new TextView(this);
                    tv.setLayoutParams(lparams_text);
                    tv.setText((String) parsedQuestionList.get(position));
                    tv.setTextSize(20);
                    tv.setTextColor(Color.DKGRAY);
                    

                    layout.addView(tv);
                }
                else if (parsedQuestionList.get(position) instanceof Bitmap) {
                    //ADD IMAGEVIEW
                    ImageKeepAspectRatio iv = new ImageKeepAspectRatio(this);
                    iv.setLayoutParams(lparams_img);
                    iv.setImageBitmap((Bitmap) parsedQuestionList.get(position));

                    PhotoViewAttacher photoViewAttacher = new PhotoViewAttacher(iv);
                    photoViewAttacher.update();

                    layout.addView(iv);
                }
            }
        }


    }

    public class AnswerListAdapter extends ArrayAdapter<Object> {
        Context mContext;
        TextView title, category;
        RelativeLayout background;


        public AnswerListAdapter(Context context, int resource) {
            super(context, resource);
            mContext = context;
        }

        public View getView(int position, View convertView, ViewGroup parent) {
            View rowView = convertView;
            Log.d("QA_APP", "Size: " + answerList.size());
            if (rowView == null) {

                LayoutInflater inflater = getLayoutInflater();
                rowView = inflater.inflate(R.layout.list_row, null, true);

                //INITIALIZE CUSTOM LIST ROW ELEMENTS
                title = (TextView) rowView.findViewById(R.id.list_title);
                category = (TextView) rowView.findViewById(R.id.list_category);
                background = (RelativeLayout) rowView.findViewById(R.id.list_bg);

                //CODE TO POPULATE DATA
                title.setText("Answer Title");
                category.setText("Short blurb");

                if(answerList.get(position) == (Integer) 2) {
                    background.setBackgroundColor(Color.GREEN);
                }

            }

            return rowView;

        }


    }
}