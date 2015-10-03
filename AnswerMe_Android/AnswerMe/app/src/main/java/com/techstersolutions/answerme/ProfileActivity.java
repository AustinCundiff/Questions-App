package com.techstersolutions.answerme;

import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.PagerTabStrip;
import android.support.v4.view.ViewPager;
import android.support.v7.app.ActionBar;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import java.util.ArrayList;


public class ProfileActivity extends ActionBarActivity {
    ViewPager pager;
    TabPagerAdapter tabPagerAdapter;
    PagerTabStrip pagerTabStrip;
    public Toolbar toolbar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);

        pager = (ViewPager) findViewById(R.id.pager);
        tabPagerAdapter = new TabPagerAdapter(getSupportFragmentManager());
        pagerTabStrip = (PagerTabStrip) findViewById(R.id.pager_tab_strip);
        pager.setOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

            }

            @Override
            public void onPageSelected(int position) {

            }

            @Override
            public void onPageScrollStateChanged(int state) {

            }
        });
        pager.setOffscreenPageLimit(2);
        pager.setAdapter(tabPagerAdapter);


    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_profile, menu);
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

    public class TabPagerAdapter extends FragmentStatePagerAdapter {
        Bundle bundle;

        public TabPagerAdapter(FragmentManager fm) {
            super(fm);
        }

        @Override
        public Fragment getItem(int i) {
            switch (i) {
                case 0:
                    return Profile_QuestionListFragment.newInstance(0);
                case 1:
                    return Profile_QuestionListFragment.newInstance(1);
            }
            return null;
        }

        @Override
        public int getCount() {
            // TODO Auto-generated method stub
            return 2; //# tabs
        }

        public Profile_QuestionListFragment getFragmentWithBundle(int type) {
            bundle = new Bundle();
            bundle.putInt("type", type);

            Profile_QuestionListFragment f = new Profile_QuestionListFragment();
            f.setArguments(bundle);
            return f;
        }

        @Override
        public CharSequence getPageTitle(int position) {
            switch(position) {
                case 0:
                    return "My Questions";
                case 1:
                    return "Questions Answered";
            }
            return null;
        }
    }

    public static class Profile_QuestionListFragment extends Fragment {
        int type = -1; //two types, 0 = questions, 1 = answered
        ListView listView;
        ProfileListAdapter listAdapter;
        ArrayList<String> profileListItems;

        public static final Profile_QuestionListFragment newInstance(int type) {
            Profile_QuestionListFragment pqf = new Profile_QuestionListFragment();
            Bundle bdl = new Bundle(1);
            bdl.putSerializable("type", type);
            pqf.setArguments(bdl);
            return pqf;
        }



        @Override
        public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
            type = getArguments().getInt("type");

            View v = inflater.inflate(R.layout.profile_frag_list, container, false);
            listView = (ListView) v.findViewById(R.id.listview);
            listAdapter = new ProfileListAdapter(getActivity().getApplicationContext(), R.layout.profile_frag_list);

            profileListItems = new ArrayList<String>();
            if(type == 0) {
                profileListItems.add("I asked this question 1");
                profileListItems.add("I asked this question 2");
                profileListItems.add("I asked this question 3");
            } else if(type == 1) {
                profileListItems.add("I answered this question 1");
                profileListItems.add("I answered this question 2");
                profileListItems.add("I answered this question 3");
                profileListItems.add("I answered this question 4");

            }

            listAdapter.addAll(profileListItems);
            listView.setAdapter(listAdapter);

            return v;
        }

        private class ProfileListAdapter extends ArrayAdapter<String> {
            Context c;
            TextView title, category;
            ImageView thumbnail;

            public ProfileListAdapter(Context context, int resource) {
                super(context, resource);
                c = context;
            }

            public View getView(int position, View convertView, ViewGroup parent)  {
                View rowView = convertView;
                if (rowView == null) {
                    LayoutInflater inflater = getActivity().getLayoutInflater();
                    rowView = inflater.inflate(R.layout.list_row, null, true);

                    title = (TextView) rowView.findViewById(R.id.list_title);
                    category = (TextView) rowView.findViewById(R.id.list_category);
                    thumbnail = (ImageView) rowView.findViewById(R.id.list_thumbnail);
                }

                title.setText(profileListItems.get(position));
                return rowView;
            }
        }
    }

    public void openSettings(View v) {
        startActivity(new Intent(this, SettingsActivity.class));
    }
}
