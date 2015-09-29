# IDX-Broker-Partners-Check-for-Approved-MLS

Standard Disclaimer: This code is not official IDX Broker code. It does use their API, but in NO WAY is it supported by IDX Broker. DO NOT contact IDX Broker for any support of this code.

Use your IDX Broker Developer Partner key to check all enabled accounts for an approved MLS. Should any enabled account NOT have an approved MLS shoot yourself an email with taht report. Suggested to run no more than weekly.

Since not all MLSs approve IDX Broker accounts in the same timeframe, a weekly report of accounts will help keep process moving along.

This script requires a IDX Broker Developer Partner API key.

It calls for all clients, then checks all enabled clients for an approved MLS. If an account is approved and there is no approved MLS, the Accoutn ID is added to an email report and sent to the email address specified.
