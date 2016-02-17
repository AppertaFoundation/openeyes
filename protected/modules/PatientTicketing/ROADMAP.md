Patient Ticketing Roadmap
=========================

The basic data model and structure for Patient Ticketing is in place. However, there are several pieces of functionality
that have yet to be implemented. This document is intended to capture those requirements.

Administration
==============

1. Limiting users to be able to see tickets on specific pathways or only certain queues. When more pathways are configured,
it will be necessary to provide a filtering of what queues users can see and operate on.

2. Nested pathways. Whilst the data model supports it, the admin UI does not allow a queue to be reached from more than
one queue, so pathways are linear. The admin needs to be updated to support multiple routes to the same queue.

3. Assignment field admin. At the moment this must be entered as JSON data structure, which is rather complex and leaves
it as a developer task.

User Interface
==============

1. When multiple pathways are configured, it will be a useful feature for users to be able to pick the initial queue
they are interested in to define the pathway they want to view tickets on.

2. It may prove necessary to define the view columns based on the initial queue, so that the view can be configured more
cleanly than simply capturing data in the ticket info blob - however we'll still want to cache this information to make it
quicker to retrieve the data for display.

3. Support the ability to create simple events directly from the process of moving tickets to a new queue - specifically
we may want to ensure that any information (notes/comments) entered create an "annotation event" on the patient record
to ensure any clinical notes are transparently visible to the user. Other hooks might be useful such as sending messages
to users with responsibility for specific queues.
