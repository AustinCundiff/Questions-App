package com.techstersolutions.answerme;

/**
 * QuestionListActivity.java
 * Techster Solutions
 * Jason John
 */


import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;

import android.widget.ArrayAdapter;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.view.animation.AlphaAnimation;
import android.view.animation.Animation;
import android.view.animation.AnimationSet;
import android.view.animation.TranslateAnimation;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.support.v7.widget.Toolbar;
import android.support.v7.widget.SearchView;
import android.app.SearchManager;
import android.support.v4.view.MenuItemCompat;
import android.support.v7.widget.SearchView.OnQueryTextListener;
import android.support.v4.widget.SwipeRefreshLayout;
import android.os.Handler;

/*
* ADDING NEW ITEM TO LIST
* 
* list.add("Test");
listAdapter.clear();
listAdapter.addAll(list);
listAdapter.notifyDataSetChanged();
*/


public class QuestionListActivity extends ActionBarActivity  {
    public static final String ROOT_ADDR = "http://dev.qa.switchit001.com/dev2/request.php";
    private static final String TAG = "QA_APP";
    public Intent intent;
    public Toolbar toolbar;
    String sessionKey, url;
    Button askButton;
    ListView listView;
    ArrayList<QuestionObject> mList;
    QuestionListAdapter listAdapter;
    private SwipeRefreshLayout mSwipeRefreshLayout;
   private Spinner mSpinner, mSpinner2;
    String[] actions = new String[] {
            "Bookmark",
            "Subscribe",
            "Share"
    };
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_question_list);

        int currentapiVersion = android.os.Build.VERSION.SDK_INT;
        if (currentapiVersion >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

            getWindow().setStatusBarColor(getResources().getColor(R.color.primaryColor));
            // Do something for froyo and above versions
        }

/**********************************************/
//Initializing user interface

        toolbar = (Toolbar) findViewById(R.id.app_bar);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowTitleEnabled(false);

        Spinner mSpinner = (Spinner) findViewById(R.id.spinner_nav);
        Spinner mSpinner2 = (Spinner) findViewById(R.id.spinner_fire);

        mSpinner.setBackground(getResources().getDrawable(R.drawable.rounded_white_bg));

        ArrayAdapter spinnerAdapter = ArrayAdapter.createFromResource(this,
                R.array.categories, R.layout.cat_spinner_appearance);
        ArrayAdapter fireAdapter = ArrayAdapter.createFromResource(this,
                R.array.fireCategories, android.R.layout.simple_spinner_dropdown_item);

        spinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        fireAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

        mSpinner.setAdapter(spinnerAdapter);
        mSpinner2.setAdapter(fireAdapter);



        askButton = (Button) findViewById(R.id.askButton);
        listView = (ListView) findViewById(R.id.listview);
        mSwipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swiperefresh);
        mSwipeRefreshLayout.setProgressBackgroundColor(android.R.color.transparent);
        mList = new ArrayList<QuestionObject>();
        listAdapter = new QuestionListAdapter(this, R.layout.list_row);
/**********************************************/



//execute async to get question data
        sessionKey = getSharedPreferences("com.switchit001.qaapp", Context.MODE_PRIVATE).getString("sessionKey", "ERR");

        new AsyncHandler().execute("fish");

        mSwipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {



                  listView = (ListView) findViewById(R.id.listview);


                mList = new ArrayList<QuestionObject>();
                listAdapter = new QuestionListAdapter(getApplicationContext(), R.layout.list_row);
/**********************************************/

//execute async to get question data
                sessionKey = getSharedPreferences("com.switchit001.qaapp", Context.MODE_PRIVATE).getString("sessionKey", "NULL");

//Building URL for HTTP Request
                url = ROOT_ADDR
                        + "?session=" + sessionKey
                        + "&msgId=" + 3
                        + "&par0=" + 1
                        + "&par1=" //optional search string
                        + "&par2=" + 1
                        + "&par3=" + 0;

//Execute AsyncTask on url

                new AsyncHandler().execute(url);

                new Handler().postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        mSwipeRefreshLayout.setRefreshing(false);
                    }
                }, 2000);




            }
        });

        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                QuestionObject questionObject = listAdapter.getItem(position);
                Intent intent = new Intent(getApplicationContext(), AnswerActivity.class);
                intent.putExtra("questionID", questionObject.getId());
                startActivity(intent);
                overridePendingTransition(R.anim.expand, R.anim.fade_out);
            }
        });

//OnClickListener for the "Ask!" Button. Takes user to camera preview
        askButton.setOnClickListener(new OnClickListener() {

            @Override
            public void onClick(View v) {
                startActivity(new Intent(getApplicationContext(), QABuilder2.class));
                overridePendingTransition(R.anim.slide_up, R.anim.fade_out);
            }
        });
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Search

        getMenuInflater().inflate(R.menu.menu_main, menu);
        SearchManager SManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);

        MenuItem searchMenuItem = menu.findItem(R.id.action_search);


        SearchView searchViewAction = (SearchView) MenuItemCompat.getActionView(searchMenuItem);
        searchViewAction.setSearchableInfo(SManager.getSearchableInfo(getComponentName()));
        searchViewAction.setIconifiedByDefault(false);
        searchViewAction.setOnQueryTextListener(new OnQueryTextListener()
        {
            public boolean onQueryTextChange(String text) {
                return false;
            }

            public boolean onQueryTextSubmit(String text) {

                url = ROOT_ADDR
                        + "?session=" + sessionKey
                        + "&msgId=" + 3
                        + "&par0=" + 1
                        + "&par1=" + text.replaceAll(" ", "%20")//optional search string
                        + "&par2=" + 1
                        + "&par3=" + 0;
                mList = new ArrayList<QuestionObject>();
                listAdapter = new QuestionListAdapter(getApplicationContext(), R.layout.list_row);
                new AsyncHandler().execute(url);
                return true;
            }

        });

        //profile
        MenuItem profileItem = menu.findItem(R.id.action_profile);
        profileItem.setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
            @Override
            public boolean onMenuItemClick(MenuItem item) {
                startActivity(new Intent(getApplicationContext(), ProfileActivity.class));
                overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                return true;
            }
        });





        return super.onCreateOptionsMenu(menu);


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

    /*
    * getBitmapFromServer
    * Gets the thumbnail image from the server. The URL can be
    * accessed from a QuestionObject with QuestionObject.getThumbPath()
    */
    public Bitmap getBitmapFromServer(String url) {
        Log.d("QA_APP", "Downloading Image...");
        Bitmap bmp = null;
        try {
            InputStream in = new java.net.URL(url).openStream();
            bmp = BitmapFactory.decodeStream(in);
        } catch (Exception e) {
            Log.e("Error", e.getMessage());
            e.printStackTrace();
        }
        return bmp;
    }

    /*
    * Question List Adapter
    * Custom ListView adapter that generates a row on the list based on the contents of
    * the list of questions gathered from the server. R.layout.list_row is the layout for
    * each row.
    */
    public class QuestionListAdapter extends ArrayAdapter<QuestionObject>  {
        Context mContext;
        TextView title, category;
        ImageView thumbnail;
        private int mLastFirstVisibleItem;
        private boolean mIsScrollingUp;


        public QuestionListAdapter(Context context, int resource) {
            super(context, resource);
            mContext = context;
        }

        public View getView(int position, View convertView, ViewGroup parent)  {
            View rowView = convertView;

            Log.d("QA_APP", "Size: " + mList.size());
            int lastposition = position;
            AnimationSet set = new AnimationSet(true);

            if (rowView == null) {

                LayoutInflater inflater = getLayoutInflater();
                rowView = inflater.inflate(R.layout.list_row, null, true);

                //INITIALIZE CUSTOM LIST ROW ELEMENTS
                title = (TextView) rowView.findViewById(R.id.list_title);
                category = (TextView) rowView.findViewById(R.id.list_category);
                thumbnail = (ImageView) rowView.findViewById(R.id.list_thumbnail);

                //CODE TO POPULATE DATA
                title.setText(mList.get(position).getTitle());
                category.setText("Category: " + mList.get(position).getCategory());

                //Currently "NO PREV"
                thumbnail.setImageBitmap(mList.get(position).getThumbnail());

            }

            Animation animation = new AlphaAnimation(0.0f, 1.0f);
            animation.setDuration(800);
            set.addAnimation(animation);

            animation = new TranslateAnimation(
                    Animation.RELATIVE_TO_SELF, 0.0f,Animation.RELATIVE_TO_SELF, 0.0f,
                    Animation.RELATIVE_TO_SELF, 1.0f,Animation.RELATIVE_TO_SELF, 0.0f
            );

           Log.i("",position+" - "+lastposition);

            if (position >= lastposition)
            {
                animation = new TranslateAnimation(Animation.RELATIVE_TO_SELF,
                        0.0f, Animation.RELATIVE_TO_SELF, 0.0f,
                        Animation.RELATIVE_TO_SELF, 1.0f,
                        Animation.RELATIVE_TO_SELF, 0.0f);

            }
            else
                animation = new TranslateAnimation(Animation.RELATIVE_TO_SELF,
                        0.0f, Animation.RELATIVE_TO_SELF, 0.0f,
                        Animation.RELATIVE_TO_SELF, -1.0f,
                        Animation.RELATIVE_TO_SELF, 0.0f);

            animation.setDuration(200);
            set.addAnimation(animation);

            rowView.startAnimation(set);
            if(mSwipeRefreshLayout.isRefreshing() == true)
            {
                mSwipeRefreshLayout.setRefreshing(false);
            }
            lastposition = position;
            return rowView;

        }


    }

    /* AsyncHandler
    * Handles AsyncTask (Can't do network operations on the main (UI) thread.
    * Sends HTTP Request to Server and parses JSON to get question data.
    * Question data is put into a QuestionObject object and the QuestionObject
    * is placed into an ArrayList.
    */
    private class AsyncHandler extends AsyncTask<String, Void, Void> {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

        }

        @Override
        protected Void doInBackground(String... params) {
            try {
                mList = DbHandler.getQuestionList(getApplicationContext(), params[0], 1);
            } catch (JSONException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
            }
            Log.d(TAG, "SIZE: " + mList.size());
            return null;
        }


        protected void onPostExecute(Void result) {
//populate the list after the HTTP Request is complete.

            listView.setAdapter(listAdapter);
            listAdapter.addAll(mList);
            listAdapter.notifyDataSetChanged();


        }
    }
}