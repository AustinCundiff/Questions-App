package com.techstersolutions.answerme;

import android.support.annotation.NonNull;
import android.view.View;

import com.nhaarman.listviewanimations.itemmanipulation.DynamicListView;
import com.nhaarman.listviewanimations.itemmanipulation.dragdrop.DragAndDropHandler;
import com.nhaarman.listviewanimations.itemmanipulation.dragdrop.DragAndDropListViewWrapper;
import com.nhaarman.listviewanimations.itemmanipulation.dragdrop.DynamicListViewWrapper;

/**
 * Created by admin on 1/29/15.
 */
public class DragDropHandler2 extends DragAndDropHandler {
    public DragDropHandler2(@NonNull final DynamicListView dynamicListView) {
        this(new DynamicListViewWrapper(dynamicListView));
    }

    public DragDropHandler2(@NonNull final DragAndDropListViewWrapper dragAndDropListViewWrapper) {
        super(dragAndDropListViewWrapper);
    }

    private void switchIfNecessary() {

    }

    private void switchViews(final View switchView, final long switchId, final float translationY) {
        
    }
}
