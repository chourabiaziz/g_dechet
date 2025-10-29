from django.db import models
from django.contrib.auth.hashers import make_password

# This model maps to the existing `user` table from the Symfony app. It
# is generated as a minimal representation — adjust fields to exactly match
# your current schema. Set managed = False to prevent Django from trying to
# manage the existing table unless you want to migrate it under Django.

class LegacyUser(models.Model):
    id = models.BigAutoField(primary_key=True)
    email = models.CharField(max_length=180, unique=True)
    roles = models.TextField(blank=True, null=True)
    password = models.CharField(max_length=255)
    name = models.CharField(max_length=50, blank=True, null=True)
    prename = models.CharField(max_length=50, blank=True, null=True)
    phone = models.CharField(max_length=50, blank=True, null=True)
    registrationDate = models.DateTimeField(blank=True, null=True)
    block = models.BooleanField(blank=True, null=True)
    countryCode = models.CharField(max_length=10, blank=True, null=True)

    class Meta:
        db_table = 'user'
        managed = False

    def set_password(self, raw_password):
        self.password = make_password(raw_password)
        return self
