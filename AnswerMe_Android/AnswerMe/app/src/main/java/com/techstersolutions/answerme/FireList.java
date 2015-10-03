package com.techstersolutions.answerme;
import android.content.Intent;
import android.provider.Settings;
import android.support.v4.view.ActionProvider;

import android.content.Context;
import android.util.Log;

import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.MenuItem.OnMenuItemClickListener;
import android.view.View;
import android.widget.ImageButton;
import android.widget.Toast;

/**
 * Created by Aust1_000 on 1/18/2015.
 */
public class FireList extends ActionProvider implements OnMenuItemClickListener {

    static final int LIST_LENGTH = 3;
    private static final Intent sSettingsIntent = new Intent(Settings.ACTION_SETTINGS);
    Context mContext;

    public FireList(Context context) {
        super(context);
        mContext = context;
    }

    @Override
    public View onCreateActionView() {
        LayoutInflater layoutInflater = LayoutInflater.from(mContext);
        View view = layoutInflater.inflate(R.layout.cat_spinner_appearance, null);
        ImageButton button = (ImageButton) view.findViewById(R.id.home);

        return view; // null を返してもいい
    }

    @Override
    public boolean onPerformDefaultAction() {
        Log.d(this.getClass().getSimpleName(), "onPerformDefaultAction");
        mContext.startActivity(sSettingsIntent);
        return true;
    }

    @Override
    public boolean hasSubMenu() {
        Log.d(this.getClass().getSimpleName(), "hasSubMenu");

        return false;
    }

/*
    @Override
    public void onPrepareSubMenu(SubMenu subMenu) {
        Log.d(this.getClass().getSimpleName(), "onPrepareSubMenu");

        subMenu.clear();

        PackageManager manager = mContext.getPackageManager();
        List<ApplicationInfo> applicationList = manager
                .getInstalledApplications(PackageManager.GET_ACTIVITIES);

        for (int i = 0; i < Math.min(LIST_LENGTH, applicationList.size()); i++) {
            ApplicationInfo appInfo = applicationList.get(i);

            subMenu.add(0, i, i, manager.getApplicationLabel(appInfo))
                    .setIcon(appInfo.loadIcon(manager))
                    .setOnMenuItemClickListener(this);
        }

        if (LIST_LENGTH < applicationList.size()) {
            subMenu = subMenu.addSubMenu(Menu.NONE, LIST_LENGTH, LIST_LENGTH,
                    "hoge");

            for (int i = 0; i < applicationList.size(); i++) {
                ApplicationInfo appInfo = applicationList.get(i);

                subMenu.add(0, i, i, manager.getApplicationLabel(appInfo))
                        .setIcon(appInfo.loadIcon(manager))
                        .setOnMenuItemClickListener(this);
            }
        }
    }*/

    @Override
    public boolean onMenuItemClick(MenuItem item) {
        Toast.makeText(mContext, item.getTitle(), Toast.LENGTH_SHORT).show();
        return true;
    }
}



